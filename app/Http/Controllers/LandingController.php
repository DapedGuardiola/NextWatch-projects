<?php

namespace App\Http\Controllers;
use App\Services\LandingService;
use App\Services\ActorService;
use App\Services\TopChartedService;

class LandingController extends Controller
{
    protected $landingService;
    protected $actorService;
    protected $topChartedService;

    public function __construct(LandingService $landingService, ActorService $actorService, TopChartedService $topChartedService)
    {
        $this->landingService = $landingService;
        $this->actorService   = $actorService;
        $this->topChartedService = $topChartedService;
    }

    public function index()
    {
        $moviesByGenre = $this->getMoviesByGenre();
        $popularMovie  = $this->getBestMovie();
        $actors        = $this->actorService->getActor();

        return view('landing', compact('moviesByGenre', 'popularMovie', 'actors'));
    }

    public function getBestMovie()
    {
        return $this->topChartedService->getAllTimeBest()->first();
    }

    public function getMoviesByGenre()
    {
        return $this->topChartedService->getBestMoviesByGenre();
    }
}