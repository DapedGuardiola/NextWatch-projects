<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;

class LandingService
{
    public function getMoviesByGenre()
    {
        $genres = Genre::all();

        $result = [];
        foreach ($genres as $genre) {
            $movies = Movie::select([
                'tmdb_movie_id',
                'title',
                DB::raw('YEAR(release_date) as year'),
                'rating',
                'overview',
                'runtime',
                'poster_path',
            ])
            ->with('genres.genre:map_id,name')
            ->whereHas('genres', function ($q) use ($genre) {
                $q->where('movie_genres.map_genre_id', $genre->map_id);
            })
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

            if ($movies->isNotEmpty()) {
                $result[] = [
                    'genre'  => $genre->name,
                    'movies' => $movies,
                ];
            }
        }

        return $result;
    }

    public function getPopularMovie()
    {
        return Movie::orderBy('popularity', 'desc')->first();
    }
}