<?php

namespace App\Jobs;

use App\Models\LogActivityModel;
use App\Services\FlaskService;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Movie;

class ReevalTriger implements ShouldQueue, ShouldBeUnique
{
    use Queueable;
    public int $uniqueFor = 300;
    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId, protected array $userLogIds)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activityWeights = [
            'click'         => 0.1,
            'search'        => 0.1,
            'watch_trailer' => 0.2,
            'watchlist'     => 0.3,
            'favorite'      => 0.3,
        ];
        $flaskService = new FlaskService();
        $activities = LogActivityModel::whereIn('id', $this->userLogIds)->get();
        $movie_ids = $activities->pluck('tmdb_movie_id')->toArray();
        $totalWeight = $activities->sum(fn($a) => $activityWeights[$a->type] ?? 0);
        if ($totalWeight < 0.5) return;
         $movieData = Movie::whereIn('tmdb_movie_id', $movie_ids)->with([
            'genres:tmdb_movie_id,map_genre_id',
        ])->get()
        ->map(fn($m) => [
            'tmdb_movie_id' => $m->tmdb_movie_id,
            'genre_ids' => $m->genres->pluck('map_genre_id')->toArray(),
        ])->toArray();
        $intersectmovieIds = $flaskService->getIntersectGenres($movieData);
        if (count($intersectmovieIds) >= 1) {
           
            // >= 3 pasang relate → trigger
            
            RecomputePersona::withChain([new ComputeRecommendation($this->userId)])->dispatch($this->userId, $intersectmovieIds,$this->userLogIds);

            LogActivityModel::whereIn('id', $this->userLogIds)->update(['is_evaluated' => true]);
        }   
    }
}
