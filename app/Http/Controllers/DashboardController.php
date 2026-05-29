<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;
use App\Services\ActorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $actorService;
    public function __construct(DashboardService $dashboardService, ActorService $actorService){
        $this->dashboardService = $dashboardService;
        $this->actorService = $actorService;
    }
    public function index(){

        if (Auth::user()->is_personalized == 0) {
            return redirect()->route('personalization.index');
        }

        $movies = $this->dashboardService->getMovie();
        $popularMovie = $this->dashboardService->getPopularMovie();
        $actors = $this->actorService->getActor();
        return view('dashboard', compact(['movies','popularMovie','actors']));
    }
    
    public function getActorMovie($id){
        $actorsData = $this->actorService->getActorMovies($id);
        return view('pages.actor-detail',compact('actorsData'));
    }
}
