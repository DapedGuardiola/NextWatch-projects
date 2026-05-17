<?php

namespace App\Providers;

use App\Services\DiscoverService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.discover-modal', function ($view) {
            $discoverService = app(DiscoverService::class);
            $view->with([
                'genres'    => $discoverService->getGenres(),
                'languages' => $discoverService->getLanguages(),
            ]);
        });
    }
}
