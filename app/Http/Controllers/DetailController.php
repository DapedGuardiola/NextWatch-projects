<?php

namespace App\Http\Controllers;

use App\Services\DetailService;
use App\Services\LogActivityService;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    protected $detailService;
    protected $logActivityService;
    protected $commentService;
    public function __construct(DetailService $detailService, LogActivityService $logActivityService, CommentService $commentService)
    {
       $this->detailService = $detailService;
       $this->logActivityService = $logActivityService;
       $this->commentService = $commentService;
    }
    public function index(int $id) {
        $similarMovies = $this->detailService->filterSimilar($id);
        $movie = $this->detailService->getSelectedMovie($id);
        $userId = Auth::id();

        if($movie){
            $this->logActivityService->click(['user_id'=>$userId,'movie_id'=>$id]);
        }

        $genreNames = $movie->genres->pluck('genre.name')->filter()->unique()->toArray();
        $comments = $this->commentService->getCommentsByMovie($movie->tmdb_movie_id);
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
            'genreNames',
            'comments'
        ));
    }
}
