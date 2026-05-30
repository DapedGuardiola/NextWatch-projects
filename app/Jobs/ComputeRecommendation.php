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
                'genres' => $movie->genres,
                'release_year' => (
                    $movie->release_date
                    ? date(
                        'Y',
                        strtotime($movie->release_date)
                    )
                    : null
                ),
                'actors' => $movie->actors->pluck('tmdb_actor_id'),
                'directors' => $movie->directors->pluck('tmdb_director_id'),
                'normalizedData' => $movie->normalizedData,
            ];
        })->toArray();
        if ($userGenres && $movies) {
            $recommendation_ids = $flaskService->computeRecommendation($userGenres, $userTastes, $movies);
            if (!$recommendation_ids) {
                $recommendation_ids = [];
            }
            foreach ($recommendation_ids as $i) {
                UserRecommendation::insert([
                    'user_id' => $this->userId,
                    'tmdb_movie_id' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            User::where('id', $this->userId)->update(['persona_ready' => true]);
        }
    }
}
