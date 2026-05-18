<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function store($movie)
    {
        Watchlist::firstOrCreate([
            'user_id' => auth()->id(),
            'movie_id' => $movie,
        ]);

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