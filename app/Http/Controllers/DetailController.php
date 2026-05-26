<?php

namespace App\Http\Controllers;

use App\Services\DetailService;
use App\Services\LogActivityService;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    protected $detailService;
    protected $logActivityService;
    protected $user_id;
    public function __construct(DetailService $detailService, LogActivityService $logActivityService)
    {
       $this->detailService = $detailService;
       $this->logActivityService = $logActivityService;
       $this->user_id = auth()->id();
    }
    public function index(int $id) {
    $similarMovies = $this->detailService->filterSimilar($id);
    $logActivityService = new LogActivityService();
    $logActivityService->click(['user_id'=>$this->user_id,'movie_id'=>$id]);
    $movie = $this->detailService->getSelectedMovie($id);
    $genreNames = $movie->genres->pluck('genre.name')->filter()->unique()->toArray();
    $comments = $movie->comments()
        ->with('user')
        ->latest()
        ->get();
    $isInWatchlist = \App\Models\Watchlist::where(
        'user_id',
        auth()->id()
    )
    ->where(
        'movie_id',
        $movie->tmdb_movie_id
    )
    ->exists();

    $isFavorite = \App\Models\Favorite::where(
        'user_id',
        auth()->id()
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
