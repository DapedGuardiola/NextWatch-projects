<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopChartedService
{
    public function getTopPopularMoviesAllTime($limit = 10)
    {
        $movies = Movie::select([
            'tmdb_movie_id',
            'title',
            'poster_path',
            'popularity',
        ])
            ->orderByRaw('popularity DESC')
            ->limit($limit)
            ->get();

        Log::info('Top popular movies all time retrieved', ['count' => $movies->count()]);

        return $movies->map(function ($movie) {
            return [
                'id' => $movie->tmdb_movie_id,
                'title' => $movie->title,
                'poster_path' => $movie->poster_path,
                'popularity' => $movie->popularity,
            ];
        });
    }

    public function getBestMoviesByGenre($moviesPerGenre = 10)
    {
        $genres = DB::table('genres')->get();
        
        $moviesByGenre = [];

        foreach ($genres as $genre) {
            $movies = Movie::select([
                'movies.tmdb_movie_id',
                'movies.title',
                DB::raw('YEAR(movies.release_date) as year'),
                'movies.rating',
                'movies.rating_count',
                'movies.overview',
                'movies.runtime',
                'movies.poster_path',
                'movies.popularity',
            ])
                ->join('movie_genres', 'movies.tmdb_movie_id', '=', 'movie_genres.tmdb_movie_id')
                ->where('movie_genres.map_genre_id', '=', $genre->map_id)
                ->orderByRaw('movies.popularity DESC')
                ->limit($moviesPerGenre)
                ->get();

            if ($movies->count() > 0) {
                $moviesByGenre[$genre->name] = $movies->map(function ($movie) {
                    return [
                        'id' => $movie->tmdb_movie_id,
                        'title' => $movie->title,
                        'year' => $movie->year,
                        'rating' => $movie->rating,
                        'rating_count' => $movie->rating_count,
                        'overview' => $movie->overview,
                        'runtime' => $movie->runtime,
                        'poster_path' => $movie->poster_path,
                        'popularity' => $movie->popularity,
                        'genres'     => $movie->genres->pluck('genre.map_id')->filter()->values()->toArray(),
                    ];
                })->toArray();
            }
        }

        Log::info('Best movies by genre retrieved', ['genres_count' => count($moviesByGenre)]);

        return $moviesByGenre;
    }
}
