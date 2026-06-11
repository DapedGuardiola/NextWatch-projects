<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FlaskService;
use App\Models\UserGenre;
use App\Models\Movie;
use App\Models\UserRecommendation;
use App\Models\UserTaste;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Actor;
use App\Models\CollectionModel;
use App\Models\userMovieInteracted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ComputeRecommendation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $flaskService = new FlaskService;
        $userGenres = UserGenre::select(['genre_id', 'weight'])->where('user_id', $this->userId)->get();
        $userGenreIds = $userGenres->pluck('genre_id');
        $userTastes = UserTaste::where('user_id', $this->userId)->first()->toArray();
        $user_id = $this->userId;
        $interacted = Cache::remember("user_movie_interacted_{$this->userId}", 7600, function () use ($user_id) {
            return userMovieInteracted::where('user_id', $user_id)->get()->pluck('tmdb_movie_id')->toArray();
        });

        $incoming_ids = Cache::rememberForever("upcoming_movie_ids", function () {
            return Movie::where('status', 'upcoming')->get()->pluck('tmdb_movie_id')->toArray();
        });
        $movies = Movie::whereHas('genres', function ($query) use ($userGenreIds, $userGenres) {
            $query->whereIn('map_genre_id', $userGenreIds);
        })->with([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ])->get()->map(function ($movie) {
            return [
                'movie_id' => $movie->tmdb_movie_id,
                'genre_ids' => $movie->genres->pluck('map_genre_id')->toArray(),
                'release_year' => (
                    $movie->release_date
                    ? date(
                        'Y',
                        strtotime($movie->release_date)
                    )
                    : null
                ),
                'director_ids' => $movie->directors->pluck('tmdb_director_id')->toArray(),
                'normalizedData' => $movie->normalizedData,
            ];
        })->toArray();
        $movies = array_values(array_filter($movies, function ($movie) use ($interacted, $incoming_ids) {
            return !in_array($movie['movie_id'], $interacted)
                && !in_array($movie['movie_id'], $incoming_ids);
        }));
        if ($userGenres && $movies) {
            $recommendation_ids = $flaskService->computeRecommendation($userGenres, $userTastes, $movies);

            if ($recommendation_ids) {
                // Bungkus jadi Laravel Collection agar bisa menggunakan method implode() dll dengan aman
                $recommendation_ids = collect($recommendation_ids);

                $data = $recommendation_ids->map(fn($i) => [
                    'user_id'       => $this->userId,
                    'tmdb_movie_id' => $i,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ])->toArray();

                DB::transaction(function () use ($data) {
                    UserRecommendation::where('user_id', $this->userId)->delete();
                    UserRecommendation::insert($data);
                });
            }


            // Ambil detail film dari DB (Pastikan sertakan 'tmdb_collection_id' jika ada di tabel movies)
            $movies = Movie::selectRaw('movies.*, YEAR(release_date) as year')
                ->whereIn('tmdb_movie_id', $recommendation_ids)->get();

            // 2. Set Shared Cache untuk Detail Movie
            foreach ($movies as $movie) {
                Cache::put("movie_detail_{$movie->tmdb_movie_id}", $movie->toArray(), 7600);
            }
            $movies->load([
                'actors:tmdb_actor_id',
            ]);
            $topActors = $movies->flatMap(function ($movie) {
                return $movie->actors ? $movie->actors->pluck('tmdb_actor_id') : [];
            })->countBy()->sortDesc()->take(12)->keys()->toArray();
            Cache::put("user_rec_actor_{$this->userId}", $topActors, 7600);
            // 4. Hitung Top Koleksi
            $topCollections = $movies->whereNotNull('tmdb_collection_id')
                ->unique('tmdb_collection_id')
                ->take(7)
                ->pluck('tmdb_collection_id')->toArray();
            Cache::put("user_rec_collection_{$this->userId}", $topCollections, 7600);

            // Update Persona Status
            // --- OPTIMASI CACHING DETAIL AKTOR (ANTI N+1 QUERY) ---
            // Tarik semua data aktor sekaligus dengan 1 kali query ke DB
            $dbActors = Actor::whereIn('tmdb_actor_id', $topActors)->get()->keyBy('tmdb_actor_id');

            foreach ($topActors as $id) {
                $actorData = $dbActors->get($id); // Ambil dari hasil query yang sudah ada di memori PHP
                if ($actorData) {
                    Cache::put("actor_detail_{$id}", $actorData->toArray(), 7600);
                }
            }

            // --- OPTIMASI CACHING DETAIL KOLEKSI (ANTI N+1 QUERY) ---
            // Tarik semua data koleksi sekaligus dengan 1 kali query ke DB
            $dbCollections = CollectionModel::whereIn('tmdb_collection_id', $topCollections)->get()->keyBy('tmdb_collection_id');

            foreach ($topCollections as $id) {
                $collectionData = $dbCollections->get($id); // Ambil dari memori PHP tanpa nembak DB lagi di dalam loop
                if ($collectionData) {
                    Cache::put("collection_detail_{$id}", $collectionData->toArray(), 7600);
                }
            }
            User::where('id', $this->userId)->update(['persona_ready' => true]);
        }
    }
}
