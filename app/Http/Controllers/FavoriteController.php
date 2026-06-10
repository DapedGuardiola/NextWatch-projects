<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\userMovieInteracted;
use App\Services\LogActivityService;
use Illuminate\Support\Facades\Cache;
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
        $interacted = userMovieInteracted::firstOrCreate(['user_id'=>$user_id, 'tmdb_movie_id'=>$movie]);
        if($interacted){
            Cache::forget("user_movie_interacted_{$user_id}");
        }
        return response()->json(['success' => true]); 
    }

    public function destroy($movie)
    {
        Favorite::where('user_id', auth()->id())
            ->where('movie_id', $movie)
            ->delete();
        
        return response()->json(['success' => true]); 
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