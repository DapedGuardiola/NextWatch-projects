<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopChartedService
{
    protected $flaskService;

    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }

    public function getAllTimeBest()
    {
        // 1. Ambil semua kandidat + data normalisasi
        $candidates = Movie::select(['tmdb_movie_id'])
            ->orderByRaw('popularity DESC, rating DESC')
            ->limit(100)    
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        // 2. Payload ke Flask
        $moviesPayload = $candidates->map(function ($movie) {
            return [
                'id'           => $movie->tmdb_movie_id,
                'popularity'   => $movie->popularity   ?? 0,
                'rating'       => $movie->rating        ?? 0,
                'rating_count' => $movie->rating_count  ?? 0,
            ];
        })->toArray();

        // 3. Kirim ke Flask EDAS alltime
        try {
            $rankedIds = $this->flaskService->getAllTimeBest($moviesPayload);
        } catch (\Exception $e) {
            Log::error('Flask EDAS alltime error', ['error' => $e->getMessage()]);
            return collect();
        }

        if (empty($rankedIds)) {
            return collect();
        }

        // 4. Ambil top N sesuai urutan ranked
        $topIds = array_slice($rankedIds, 0, 10);

        // 5. Query detail movies
        $movies = Movie::select([
                'tmdb_movie_id',
                'title',
                'poster_path',
                'popularity',
                'rating',
            ])
            ->whereIn('tmdb_movie_id', $topIds)
            ->get();

        Log::info('EDAS all time best retrieved', ['count' => $movies->count()]);

        return $movies->map(function ($movie) {
            return [
                'id'          => $movie->tmdb_movie_id,
                'title'       => $movie->title,
                'poster_path' => $movie->poster_path,
                'popularity'  => $movie->popularity,
                'rating'      => $movie->rating,
            ];
        });
    }

    public function getBestMoviesByGenre()
    {
        $genres = DB::table('genres')->get();
        $moviesByGenre = [];

        foreach ($genres as $genre) {
            // 1. Ambil kandidat + data normalisasi
            $candidates = Movie::select([
                    'movies.tmdb_movie_id',
                ])
                ->with([
                    'genreVector:tmdb_movie_id,vector',
                ])
                ->join('movie_genres', 'movies.tmdb_movie_id', '=', 'movie_genres.tmdb_movie_id')
                ->where('movie_genres.map_genre_id', '=', $genre->map_id)
                ->orderByRaw('rating DESC')
                ->limit(30)
                ->get();

            if ($candidates->isEmpty()) {
                continue;
            }

            // 2. Payload ke Flask
            $moviesPayload = $candidates->map(function ($movie) {
                return [
                    'id'           => $movie->tmdb_movie_id,
                    'popularity'   => $movie->popularity   ?? 0,
                    'rating'       => $movie->rating        ?? 0,
                    'rating_count' => $movie->rating_count  ?? 0,
                ];
            })->toArray();

            // 3. Kirim ke Flask EDAS
            try {
                $rankedIds = $this->flaskService->getBestMoviesByGenre($moviesPayload);
            } catch (\Exception $e) {
                Log::error('Flask EDAS error: ' . $genre->name, ['error' => $e->getMessage()]);
                continue;
            }

            if (empty($rankedIds)) {
                continue;
            }

            // 4. Ambil top N sesuai urutan ranked
            $topIds = array_slice($rankedIds, 0, 10);

            // 5. Query detail movies
            $movies = Movie::select([
                    'tmdb_movie_id',
                    'title',
                    DB::raw('YEAR(release_date) as year'),
                    'rating',
                    'rating_count',
                    'overview',
                    'runtime',
                    'poster_path',
                    'popularity',
                ])
                ->whereIn('tmdb_movie_id', $topIds)
                ->get();

            $moviesByGenre[$genre->name] = $movies->map(function ($movie) {
                return [
                    'id'           => $movie->tmdb_movie_id,
                    'title'        => $movie->title,
                    'year'         => $movie->year,
                    'rating'       => $movie->rating,
                    'rating_count' => $movie->rating_count,
                    'overview'     => $movie->overview,
                    'runtime'      => $movie->runtime,
                    'poster_path'  => $movie->poster_path,
                    'popularity'   => $movie->popularity,
                ];
            })->toArray();
        }

        Log::info('EDAS best movies by genre', ['genres_count' => count($moviesByGenre)]);

        return $moviesByGenre;
    }
}
