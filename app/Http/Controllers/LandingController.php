<?php

namespace App\Http\Controllers;
use App\Services\LandingService;
use App\Services\ActorService;

class LandingController extends Controller
{
    protected $landingService;
    protected $actorService;

    public function __construct(LandingService $landingService, ActorService $actorService)
    {
        $this->landingService = $landingService;
        $this->actorService   = $actorService;
    }

    public function index()
    {
        $moviesByGenre = $this->landingService->getMoviesByGenre();
        $popularMovie  = $this->landingService->getPopularMovie();
        $actors        = $this->actorService->getActor();

        return view('landing', compact('moviesByGenre', 'popularMovie', 'actors'));
    }
}