<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\userMovieInteracted;
use App\Services\LogActivityService;

class FavoriteController extends Controller
{
    public function store($movie)
    {
        $logActivityService = new LogActivityService();
        
        $user_id = auth()->id();
        Favorite::firstOrCreate([
            'user_id' => $user_id,
            'movie_id' => $movie,
        ]);
        $logActivityService->favorite(['user_id'=>$user_id,'movie_id'=>$movie]);
        userMovieInteracted::firstOrCreate(['user_id'=>$user_id, 'tmdb_movie_id'=>$movie]);
        return back();
    }

    public function destroy($movie)
    {
        Favorite::where('user_id', auth()->id())
            ->where('movie_id', $movie)
            ->delete();
        
        return back();
    }

    public function index()
    {
        $favorites = Favorite::with('movie')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.favorites', compact('favorites'));
    }
}