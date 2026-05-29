<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;
use App\Services\ActorService;
use App\Models\UserGenre;
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
        
        // --- LOGIKA TUGAS 2: SECTION "FOR YOU" ---
        $forYouMovies = collect();
        
        // Cek apakah user sudah login dan sudah mengisi persona
        if (auth()->check() && auth()->user()->is_personalized) {
            // Ambil ID genre favorit user dari database
            $userGenres = UserGenre::where('user_id', auth()->id())->pluck('genre_id')->toArray();
            
            if (!empty($userGenres)) {
                // Ambil film yang memiliki map_genre_id sesuai dengan genre favorit user
                $forYouMovies = \App\Models\Movie::whereHas('genres', function($query) use ($userGenres) {
                    $query->whereIn('map_genre_id', $userGenres);
                })->with('genres.genre')->inRandomOrder()->take(10)->get(); // Ambil 10 rekomendasi acak
            }
        }

        $content = $this->dashboardService->getMainContent();  

        return view('dashboard', compact(['movies','popularMovie','actors', 'forYouMovies']));
    }
    
    public function getActorMovie($id){
        $actorsData = $this->actorService->getActorMovies($id);
        return view('pages.actor-detail',compact('actorsData'));
    }
}