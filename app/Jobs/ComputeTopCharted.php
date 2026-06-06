<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\TopChartedService;
use App\Services\FlaskService;
use Illuminate\Support\Facades\Log;

class ComputeTopCharted implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $type = 'all_time')
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $topChartedService = new TopChartedService(new FlaskService());
            
            if ($this->type === 'all_time') {
                Log::info('ComputeTopCharted: Computing all time best');
                $topChartedService->getAllTimeBest();
                Log::info('ComputeTopCharted: All time best completed');
            } elseif ($this->type === 'by_genre') {
                Log::info('ComputeTopCharted: Computing by genre');
                $topChartedService->getBestMoviesByGenre();
                Log::info('ComputeTopCharted: By genre completed');
            }
        } catch (\Exception $e) {
            Log::error('ComputeTopCharted error', [
                'type' => $this->type,
                'error' => $e->getMessage()
            ]);
        }
    }
}

