<?php

namespace App\Http\Controllers;

use App\Services\TopChartedService;
use Illuminate\Http\Request;

class TopChartedController extends Controller
{
    protected $topChartedService;

    public function __construct(TopChartedService $topChartedService)
    {
        $this->topChartedService = $topChartedService;
    }

    public function index(){
        $popularMovies = $this->topChartedService->getAllTimeBest(10);
        $moviesByGenre = $this->topChartedService->getBestMoviesByGenre(10);
        return view('topcharted', compact('popularMovies', 'moviesByGenre'));
    }
}
