<?php

namespace App\Http\Controllers;

use App\Services\DiscoverService;
use Illuminate\Http\Request;
use App\Models\Movie;

class DiscoverController extends Controller
{
    public function __construct(protected DiscoverService $discoverService) {}

    public function index()
    {
        $genres    = $this->discoverService->getGenres();
        $languages = $this->discoverService->getLanguages();
        
        $query = Movie::with('genres.genre');

        if (auth()->check() && auth()->user()->is_personalized) {
            $userGenres = \App\Models\UserGenre::where('user_id', auth()->id())->pluck('genre_id')->toArray();
            
            if (!empty($userGenres)) {
                $query->whereHas('genres', function ($q) use ($userGenres) {
                    $q->whereIn('map_genre_id', $userGenres);
                });
            }
        }

        $movies = $query->inRandomOrder()->take(24)->get(); 

        return view('discover', compact('genres', 'languages', 'movies'));
    }

    public function results(Request $request)
    {
        $genresInput    = $request->input('genres', []);
        $languagesInput = $request->input('languages', []);

        $query = Movie::with('genres.genre');

        if (!empty($genresInput)) {
            $query->whereHas('genres', function ($q) use ($genresInput) {
                $q->whereIn('map_genre_id', $genresInput);
            });
        }

        if (!empty($languagesInput)) {
            $query->whereIn('original_language', $languagesInput);
        }

        $movies = $query->get();
    
        $genres    = $this->discoverService->getGenres();
        $languages = $this->discoverService->getLanguages();

        return view('discover', compact('movies', 'genres', 'languages'));
    }
}