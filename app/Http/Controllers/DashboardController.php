<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;
use App\Services\ActorService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $actorService;
    public function __construct(DashboardService $dashboardService, ActorService $actorService){
        $this->dashboardService = $dashboardService;
        $this->actorService = $actorService;
    }
    public function index(){
        $movies = $this->dashboardService->getMovie();
        $popularMovie = $this->dashboardService->getPopularMovie();
        $actors = $this->actorService->getActor();
        return view('dashboard', compact(['movies','popularMovie','actors']));
    }

    public function topCharted(){
        $popularMovies = $this->dashboardService->getPopularMovies();
        $moviesByGenre = $this->dashboardService->getMoviesByGenre();
        return view('topcharted', compact('popularMovies', 'moviesByGenre'));
    }

    public function getActorMovie($id){
        $actorsData = $this->actorService->getActorMovies($id);
        return view('pages.actor-detail',compact('actorsData'));
    }
}
