<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\DiscoverService;
use Illuminate\Http\Request;
use App\Services\FlaskService;
use App\Models\UserGenre;
use App\Models\UserTaste;
use App\Models\Favorite;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

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
    public function testFunction(){
        $user = Auth::user();
        $userGenres = UserGenre::select(['genre_id', 'weight'])->where('user_id', $user->id)->get();
        $userGenreIds = $userGenres->pluck('genre_id');
        $userTastes = UserTaste::where('user_id', $user->id)->first()->toArray();
        $movies = Movie::whereHas('genres', function ($query) use ($userGenreIds, $userGenres) {
            $query->whereIn('map_genre_id', $userGenreIds);
        })->with([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ])->get()->map(function ($movie) {
            return [
                'movie_id' => $movie->tmdb_movie_id,
                'genres' => $movie->genres,
                'release_year' => (
                    $movie->release_date
                    ? date(
                        'Y',
                        strtotime($movie->release_date)
                    )
                    : null
                ),
                'actors' => $movie->actors->pluck('tmdb_actor_id'),
                'directors' => $movie->directors->pluck('tmdb_director_id'),
                'normalizedData' => $movie->normalizedData,
            ];
        })->toArray();
        dd($movies);
    }

}
