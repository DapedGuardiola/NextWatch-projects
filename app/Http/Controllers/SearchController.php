<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService) {}

    public function index(Request $request)
    {
        $query   = $request->input('q', '');
        $results = $this->searchService->search($query);

        return view('pages.search-results', array_merge($results, compact('query')));
    }

    public function live(Request $request)
    {
        $query   = $request->input('q', '');
        $results = $this->searchService->live($query);

        return response()->json($results);
    }
}