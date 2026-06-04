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
            'watch_trailer' => 0.2,
            'watchlist'     => 0.3,
            'favorite'      => 0.5,
        ];
        $activities = LogActivityModel::whereIn('id', $this->userLogIds)->get();
        $movie_ids = $activities->pluck('tmdb_movie_id')->toArray();
        $totalWeight = $activities->sum(fn($a) => $activityWeights[$a->type] ?? 0);
        if ($totalWeight < 0.4) return;
        if (count($movie_ids) >= 2) {
            $movieGenres = Movie::whereIn('tmdb_movie_id', $movie_ids)
                ->with('genres:tmdb_movie_id,map_genre_id')
                ->get()
                ->map(fn($m) => $m->genres->pluck('map_genre_id')->toArray());

            // Confidence check — hitung berapa film yang relate
            $relateCount = 0;
            for ($i = 0; $i < count($movieGenres); $i++) {
                for ($j = $i + 1; $j < count($movieGenres); $j++) {
                    if (count(array_intersect($movieGenres[$i], $movieGenres[$j])) > 0) {
                        $relateCount++;
                    }
                }
            } 
            // >= 3 pasang relate → trigger
            if ($relateCount >= 3) {
                RecomputePersona::withChain([new ComputeRecommendation($this->userId)])->dispatch($this->userId, $movie_ids,$this->userLogIds);
            }
            LogActivityModel::whereIn('id', $this->userLogIds)->update(['is_evaluated' => true]);
        }   
    }
}
