<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PersonalizationController extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        $movies = Movie::select('tmdb_movie_id', 'title', 'poster_path', 'rating')
            ->orderBy('popularity', 'desc')
            ->limit(30)
            ->get();

        return view('pages.personalization', compact('genres', 'movies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'genre_ids'   => 'required|array|min:1',
            'movie_ids'   => 'required|array|min:1',
        ]);

        $user = Auth::user();

        // Simpan genre favorit
        DB::table('user_genres')->where('user_id', $user->id)->delete();
        foreach ($request->genre_ids as $genreId) {
            DB::table('user_genres')->insert([
                'user_id'  => $user->id,
                'genre_id' => $genreId,
            ]);
        }

        // Simpan film favorit
        foreach ($request->movie_ids as $movieId) {
            Favorite::firstOrCreate([
                'user_id'    => $user->id,
                'movie_id'   => $movieId,
                'is_persona' => 1,
            ]);
        }

        // Update is_personalized
        DB::table('users')->where('id', $user->id)->update(['is_personalized' => 1]);

        return redirect()->route('dashboard');
    }
}