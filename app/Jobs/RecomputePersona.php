<?php

namespace App\Jobs;

use App\Models\LogActivityModel;
use App\Models\Movie;
use App\Models\UserGenre;
use App\Models\UserTaste;
use App\Services\FlaskService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecomputePersona implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId, protected array $movieIds,protected array $userLogIds )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $flask_service = new FlaskService();
        $userTastes = UserTaste::where('user_id',$this->userId)->first()->toArray();
        $userGenres = UserGenre::where('user_id',$this->userId)->get()->toArray();
        $userLog = LogActivityModel::where('id',$this->userLogIds)->get()->toArray();
        $movieData = Movie::whereIn('tmdb_movie_id', $this->movieIds)->with([
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
        $userNewTastes = $flask_service->computeReevalTastes($userTastes,$userGenres,$userLog,$movieData);
        $genres_score = $userNewTastes['preferred_genres']; 
            foreach ($genres_score as $genreId => $weight) {
                // Log::info('genre_id: ' . json_encode($genreId) .','.'Score : ' . json_encode($weight));

                UserGenre::updateOrCreate(
                    ['user_id' => $this->userId, 'genre_id' => $genreId],
                    ['weight' => $weight]
                );
            }
            UserTaste::updateOrCreate([
                'user_id'                         => $this->userId,
                'preferred_directors'              => $userNewTastes['preferred_directors'],
                'preferred_actors'                 => $userNewTastes['preferred_actors'],
                'preferred_era'                    => $userNewTastes['preferred_era'],
                'preferred_normalized_rating'     => $userNewTastes['preferred_normalized_rating'],
                'preferred_normalized_popularity' => $userNewTastes['preferred_normalized_popularity'],
                'created_at'                      => now(),
                'updated_at'                      => now(),
            ]);    
    }
}
