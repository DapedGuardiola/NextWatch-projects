<?php

namespace App\Http\Controllers;

use App\Services\DetailService;
use App\Services\LogActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    protected $detailService;
    protected $logActivityService;
    public function __construct(DetailService $detailService, LogActivityService $logActivityService)
    {
       $this->detailService = $detailService;
       $this->logActivityService = $logActivityService;
    }
    public function index(int $id) {
    $similarMovies = $this->detailService->filterSimilar($id);
    $movie = $this->detailService->getSelectedMovie($id);
    $userId = Auth::id();
    if($movie){
        $this->logActivityService->click(['user_id'=>$userId,'movie_id'=>$id]);
    }
    $genreNames = $movie->genres->pluck('genre.name')->filter()->unique()->toArray();
    $comments = $movie->comments()
        ->with('user')
        ->latest()
        ->get();
    $isInWatchlist = \App\Models\Watchlist::where(
        'user_id', $userId
    )
    ->where(
        'movie_id',
        $movie->tmdb_movie_id
    )
    ->exists();

    $isFavorite = \App\Models\Favorite::where(
        'user_id', $userId
    )
    ->where(
        'movie_id',
        $movie->tmdb_movie_id
    )
    ->exists();
    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies',
        'isInWatchlist',
        'isFavorite',
        'genreNames'
    ));
    }
}
