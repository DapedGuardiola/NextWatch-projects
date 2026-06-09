<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use Illuminate\Http\Request;
use App\Services\LogActivityService;
use App\Models\userMovieInteracted;
use Illuminate\Support\Facades\Cache;

class WatchlistController extends Controller
{
    public function store($movie)
    {
        $logActivityService = new LogActivityService;
        $user_id = auth()->id();
        Watchlist::firstOrCreate([
            'user_id' => $user_id,
            'movie_id' => $movie,
        ]);
        $logActivityService->watchlist(['user_id'=>$user_id , 'movie_id'=>$movie]);
        $interacted = userMovieInteracted::firstOrCreate(['user_id'=>$user_id, 'tmdb_movie_id'=>$movie]);
        if($interacted){
            Cache::forget("user_movie_interacted_{$user_id}");
        }
        return back();
    }

    public function destroy($movie)
    {
        Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $movie)
            ->delete();

        return back();
    }

    // FUNGSI BARU UNTUK MENAMPILKAN HALAMAN WATCHLIST
    public function index()
    {
        // Mengambil data watchlist milik user beserta filmnya dari database
        $watchlists = Watchlist::with('movie')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.watchlist', compact('watchlists'));
    }
}