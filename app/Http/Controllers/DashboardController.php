<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }
    public function index(){
        $movies = $this->dashboardService->getMovie();
        return view('dashboard', compact('movies'));
    }

    public function topCharted(){
        $popularMovies = $this->dashboardService->getPopularMovies();
        $moviesByGenre = $this->dashboardService->getMoviesByGenre();
        return view('topcharted', compact('popularMovies', 'moviesByGenre'));
    }
}
