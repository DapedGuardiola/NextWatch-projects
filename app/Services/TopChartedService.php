<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TopChartedService
{
    protected $flaskService;
    protected const CACHE_ALL_TIME_BEST = 'topcharted:all_time_best';
    protected const CACHE_BY_GENRE = 'topcharted:by_genre';
    protected const CACHE_TTL = 86400; // 24 jam

    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }

    public function getAllTimeBest()
    {
        // Cek cache terlebih dahulu
        $cacheKey = self::CACHE_ALL_TIME_BEST;
        $cachedResult = null;
        
        try {
            $cachedResult = Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Cache retrieval failed, proceeding without cache', ['error' => $e->getMessage()]);
        }

        if ($cachedResult !== null) {
            Log::info('EDAS all time best retrieved from cache');
            return collect($cachedResult);
        }

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

        $result = $movies->map(function ($movie) {
            return [
                'id'          => $movie->tmdb_movie_id,
                'title'       => $movie->title,
                'poster_path' => $movie->poster_path,
                'popularity'  => $movie->popularity,
                'rating'      => $movie->rating,
            ];
        });

        // Simpan ke cache sebagai array (untuk menghindari Collection serialization issues)
        try {
            Cache::put($cacheKey, $result->toArray(), self::CACHE_TTL);
        } catch (\Exception $e) {
            Log::warning('Cache save failed for all time best', ['error' => $e->getMessage()]);
        }

        Log::info('EDAS all time best retrieved', ['count' => $result->count()]);

        return $result;
    }

    public function getBestMoviesByGenre()
    {
        // Cek cache terlebih dahulu
        $cacheKey = self::CACHE_BY_GENRE;
        $cachedResult = null;
        
        try {
            $cachedResult = Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Cache retrieval failed for by genre, proceeding without cache', ['error' => $e->getMessage()]);
        }

        if ($cachedResult !== null) {
            Log::info('EDAS best movies by genre retrieved from cache');
            return $cachedResult;
        }

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

        // Simpan ke cache
        try {
            Cache::put($cacheKey, $moviesByGenre, self::CACHE_TTL);
        } catch (\Exception $e) {
            Log::warning('Cache save failed for by genre', ['error' => $e->getMessage()]);
        }

        Log::info('EDAS best movies by genre', ['genres_count' => count($moviesByGenre)]);

        return $moviesByGenre;
    }

    public function clearCache()
    {
        try {
            Cache::forget(self::CACHE_ALL_TIME_BEST);
            Cache::forget(self::CACHE_BY_GENRE);
        } catch (\Exception $e) {
            Log::warning('Cache clear failed', ['error' => $e->getMessage()]);
        }
        Log::info('Top charted cache cleared');
    }

    public function clearAllTimeCache()
    {
        try {
            Cache::forget(self::CACHE_ALL_TIME_BEST);
        } catch (\Exception $e) {
            Log::warning('Cache clear failed for all time best', ['error' => $e->getMessage()]);
        }
        Log::info('All time best cache cleared');
    }

    public function clearByGenreCache()
    {
        try {
            Cache::forget(self::CACHE_BY_GENRE);
        } catch (\Exception $e) {
            Log::warning('Cache clear failed for by genre', ['error' => $e->getMessage()]);
        }
        Log::info('By genre cache cleared');
    }
}
