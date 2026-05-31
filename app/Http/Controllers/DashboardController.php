<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;
use App\Services\ActorService;
use App\Models\UserGenre;
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

        $mainContent = $this->dashboardService->getMainContent(Auth::user()->id);
        $topOne = $mainContent['topOne'];
        $forYou = $mainContent['forYou'];
        $actors = $mainContent['actors'];
        $topByGenre = $mainContent['topByGenre'];
        $collections = $mainContent['collections'];
        $others = $mainContent['others'];
        return view('dashboard', compact(['topOne','forYou','actors','topByGenre','collections','others']));
    }
    
    public function getActorMovie($id){
        $actorsData = $this->actorService->getActorMovies($id);
        return view('pages.actor-detail',compact('actorsData'));
    }
}