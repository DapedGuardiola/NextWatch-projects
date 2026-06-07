<?php

namespace App\Jobs;

use App\Models\Favorite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FlaskService;
use App\Models\UserGenre;
use App\Models\UserTaste;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\years;

class ComputePersona implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public function __construct(public int $userId) {}
    public function uniqueId(): string
    {
        return (string) $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log::info('ComputePersona handle started for user: ' . $this->userId);
        $flaskService = new FlaskService;
        $userGenres = userGenre::where('user_id', $this->userId)->pluck('genre_id')->toArray();
        $userTastes = userTaste::where('user_id', $this->userId)->first();
        $userFavoritesId = Favorite::where(['user_id' => $this->userId, 'is_persona' => true])->get()->pluck('movie_id');
        $favoriteMovieData = Movie::whereIn('tmdb_movie_id', $userFavoritesId)->with([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ])->get()->map(function ($movie) {
            return [
                'tmdb_movie_id' => $movie->tmdb_movie_id,
                'genre_ids' => $movie->genres->pluck('map_genre_id')->values()
                    ->toArray(),
                'release_year' => (
                    $movie->release_date
                    ? date(
                        'Y',
                        strtotime($movie->release_date)
                    )
                    : null
                ),
                'normalized_data' => $movie->normalizedData
                    ? $movie->normalizedData->toArray()
                    : null,
                'director_ids' => $movie->directors ? $movie->directors->pluck('tmdb_director_id')->values()
                    ->toArray()
                    : null,
                'actor_ids' => $movie->actors ? $movie->actors->pluck('tmdb_actor_id')->values()
                    ->toArray()
                    : null,
            ];
        })->toArray();
        // Log::info('favoriteMovieData: ' . json_encode($favoriteMovieData));
        if (!$userTastes) {
            $response = $flaskService->ComputeNewTaste($userGenres, $favoriteMovieData);
            $userNewTastes = $response->json('userNewTastes');
            $genres_score = $userNewTastes['preferred_genres']; 
            foreach ($genres_score as $genreId => $weight) {
                // Log::info('genre_id: ' . json_encode($genreId) .','.'Score : ' . json_encode($weight));

                UserGenre::updateOrCreate(
                    ['user_id' => $this->userId, 'genre_id' => $genreId],
                    ['weight' => $weight]
                );
            }
            UserTaste::create([
                'user_id'                         => $this->userId,
                'preferred_directors'              => $userNewTastes['preferred_directors'],
                'preferred_actors'                 => $userNewTastes['preferred_actors'],
                'preferred_era'                    => $userNewTastes['preferred_era'],
                'preferred_normalized_rating'     => $userNewTastes['preferred_normalized_rating'],
                'preferred_normalized_popularity' => $userNewTastes['preferred_normalized_popularity'],
                'created_at'                      => now(),
                'updated_at'                      => now(),
            ]);
            User::where('id', $this->userId)->update(['is_personalized' => true]);
        }
    }
}
