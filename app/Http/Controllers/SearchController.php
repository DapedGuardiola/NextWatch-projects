<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use App\Services\LogActivityService;

class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService) {}

    public function index(Request $request)
    {
        $user_id = auth()->id();
        $logActivityService = new LogActivityService();

        $query   = $request->input('q', '');
        $results = $this->searchService->search($query);

        $selected_movie_id = $results['movies']->first()?->tmdb_movie_id;
        $logActivityService->search(['user_id'=>$user_id,'movie_id'=>$selected_movie_id]);
        return view('pages.search-results', array_merge($results, compact('query')));
    }   
    public function live(Request $request)
    {
        $query   = $request->input('q', '');
        $results = $this->searchService->live($query);

        return response()->json($results);
    }
}
