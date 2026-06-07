<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\TopChartedAllTime;
use App\Models\TopChartedByGenre;
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
        return collect(Cache::remember(self::CACHE_ALL_TIME_BEST, self::CACHE_TTL, function () {
            // Cek database jika cache tidak tersedia
            try {
                $databaseRecord = TopChartedAllTime::first();
                if ($databaseRecord && $databaseRecord->movies_data) {
                    Log::info('EDAS all time best retrieved from database');
                    return $databaseRecord->movies_data;
                }
            } catch (\Exception $e) {
                Log::warning('Database retrieval failed for all time best', ['error' => $e->getMessage()]);
            }

            return $this->computeAllTimeBestFromFlask();
        }));
    }

    private function computeAllTimeBestFromFlask()
    {
        // 1. Ambil semua kandidat + data normalisasi
        $candidates = Movie::select(['tmdb_movie_id'])
            ->orderByRaw('popularity DESC, rating DESC')
            ->limit(100)    
            ->get();

        if ($candidates->isEmpty()) {
            return [];
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
            return [];
        }

        if (empty($rankedIds)) {
            return [];
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
        })->toArray();

        // Simpan ke database
        try {
            TopChartedAllTime::truncate();
            TopChartedAllTime::create([
                'movies_data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::warning('Database save failed for all time best', ['error' => $e->getMessage()]);
        }

        Log::info('EDAS all time best retrieved', ['count' => count($result)]);

        return $result;
    }

    public function getBestMoviesByGenre()
    {
        return Cache::remember(self::CACHE_BY_GENRE, self::CACHE_TTL, function () {
            // Cek database jika cache tidak tersedia
            try {
                $databaseRecords = TopChartedByGenre::get();
                if ($databaseRecords->isNotEmpty()) {
                    $moviesByGenre = [];
                    foreach ($databaseRecords as $record) {
                        $moviesByGenre[$record->genre_name] = $record->movies_data;
                    }
                    
                    Log::info('EDAS best movies by genre retrieved from database');
                    return $moviesByGenre;
                }
            } catch (\Exception $e) {
                Log::warning('Database retrieval failed for by genre', ['error' => $e->getMessage()]);
            }

            return $this->computeByGenreFromFlask();
        });
    }

    private function computeByGenreFromFlask()
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
            ->with(['genres.genre'])
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
                    'genres'       => $movie->genres->pluck('genre.name')->filter()->values()->toArray(),
                ];
            })->toArray();
            
            // Simpan ke database untuk genre ini
            try {
                TopChartedByGenre::updateOrCreate(
                    ['genre_name' => $genre->name],
                    [
                        'movies_data' => $moviesByGenre[$genre->name],
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Database save failed for genre: ' . $genre->name, ['error' => $e->getMessage()]);
            }
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
