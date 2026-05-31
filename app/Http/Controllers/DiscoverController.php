<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\DiscoverService;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function __construct(protected DiscoverService $discoverService) {}

    public function index(Request $request)
    {
        $query = Movie::with('genres.genre');

        $genre = $request->query('genre');
        if ($genre) {
            $query->whereHas('genres.genre', function ($q) use ($genre) {
                $q->where('name', $genre);
            });
        }

        $language = $request->query('language');
        if ($language) {
            $query->where('original_language', $language);
        }

        $movies = $query
            ->orderBy('popularity', 'desc')
            ->take(20)
            ->get();

        return view('discover', compact('movies'));
    }

    public function results(Request $request)
    {
        $genres    = $request->input('genres', []);
        $languages = $request->input('languages', []);

        $movies = $this->discoverService->filterTest($genres, $languages);
    
        return view('discover', compact('movies'));
    }

}
