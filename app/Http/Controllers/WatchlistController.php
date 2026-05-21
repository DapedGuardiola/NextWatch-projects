<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use Illuminate\Http\Request;
use App\Services\LogActivityService;

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
        return back();
    }

    public function destroy($movie)
    {
        Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $movie)
            ->delete();

        return back();
    }
}
