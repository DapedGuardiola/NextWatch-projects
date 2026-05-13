<?php

namespace App\Http\Controllers;

use App\Services\DiscoverService;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function __construct(protected DiscoverService $discoverService) {}

    public function index()
    {
        $genres    = $this->discoverService->getGenres();
        $languages = $this->discoverService->getLanguages();

        return view('dashboard', compact('genres', 'languages'));
    }

    public function results(Request $request)
    {
        $genres    = $request->input('genres', []);
        $languages = $request->input('languages', []);

        $movies = $this->discoverService->filter($genres, $languages);
    
        return view('discover', compact('movies'));
    }

}
