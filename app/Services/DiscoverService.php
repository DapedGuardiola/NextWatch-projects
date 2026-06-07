<?php

namespace App\Services;

use App\Helpers\OriginalLanguageHelper;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\NormalizedMovieData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DiscoverService
{
    protected FlaskService $flaskService;
    protected const CACHE_PREFIX = 'discover:filter:';
    protected const CACHE_TTL = 604800; // 7 days

    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }

    /**
     * Generate cache key berdasarkan genres dan languages filter
     */
    protected function generateCacheKey(array $genres, array $languages, string $type = 'filter'): string
    {
        $genreKey = implode('-', sort($genres) ? $genres : []);
        $langKey = implode('-', sort($languages) ? $languages : []);
        return self::CACHE_PREFIX . $type . ':' . md5($genreKey . '|' . $langKey);
    }

    protected function convertCachedDataToCollection($cachedData)
    {
        return collect($cachedData)->map(function ($item) {
            $movie = (object) $item;

            if (isset($item['genres']) && is_array($item['genres'])) {
                $movie->genres = collect($item['genres'])->map(function ($genre) {
                    $g = (object) $genre;

                    // Rebuild nested genre object dari array
                    if (isset($genre['genre'])) {
                        $g->genre = (object) $genre['genre'];
                    } else {
                        $g->genre = (object) ['name' => null];
                    }

                    return $g;
                })->values();
            } else {
                $movie->genres = collect();
            }

            return $movie;
        })->values();
    }

    public function getGenres(): array
    {
        return Genre::orderBy('name')->get(['map_id', 'name'])->toArray();
    }

    public function getLanguages(): array
    {
        return Movie::select('original_language')
            ->distinct()
            ->pluck('original_language')
            ->map(fn($code) => [
                'code' => $code,
                'name' => OriginalLanguageHelper::getName($code),
            ])
            ->sortBy('name')
            ->values()
            ->toArray();
    }
    public function filterTest(array $genres, array $languages): object
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey($genres, $languages, 'filterTest');

        // Cek cache terlebih dahulu
        try {
            $cachedResult = Cache::get($cacheKey);
            if ($cachedResult !== null) {
                Log::info('Discover filterTest results retrieved from cache', [
                    'cache_key' => $cacheKey,
                    'result_count' => count($cachedResult),
                ]);
                return $this->convertCachedDataToCollection($cachedResult);
            }
        } catch (\Exception $e) {
            Log::warning('Cache retrieval failed for discover filterTest, proceeding without cache', [
                'error' => $e->getMessage()
            ]);
        }

        // 1. Filter di Laravel dulu sebelum kirim ke Flask
        $query = Movie::select([
            'tmdb_movie_id',
            'original_language',
        ])->with(['genreVector:tmdb_movie_id,vector']);

        $user_vector = array_fill(0, 20, 0);
        $adjusted_genres = array_map(fn($g) => $g - 1, $genres);
        $user_vector = array_replace($user_vector, array_fill_keys($adjusted_genres, 1));

        // Filter bahasa
        if (!empty($languages)) {
            $query->whereIn('original_language', $languages);
        }

        // Filter genre via movie_genres
        if (!empty($genres)) {
            $query->whereHas('genres', function ($q) use ($genres) {
                $q->whereIn('map_genre_id', $genres);
            });
        }
        $movies = $query->pluck('tmdb_movie_id')->toArray();

        // 2. Kirim ke Flask hanya data yang sudah difilter
        $rankedIds = $this->flaskService->getDiscoverTest($user_vector, $movies);

        if (empty($rankedIds) || !is_array($rankedIds)) {
            Log::error('Respon Flask bermasalah atau bukan array', ['data' => $rankedIds]);
            return collect(); // Return kosong secara damai, ANTI CRASH!
        }

        // 3. Query movie untuk tampilan
        $result = Movie::select([
            'tmdb_movie_id',
            'title',
            DB::raw('YEAR(release_date)as year'),
            'poster_path',
            'rating',
            'overview',
            'runtime',
        ])->with(['genres.genre'])
            ->whereIn('tmdb_movie_id', $rankedIds)
            ->orderByRaw('FIELD(tmdb_movie_id, ' . implode(',', $rankedIds) . ')')
            ->get();

        // Simpan ke cache
        try {
            Cache::put($cacheKey, $result->toArray(), self::CACHE_TTL);
            Log::info('Discover filterTest results cached', [
                'cache_key' => $cacheKey,
                'ttl' => self::CACHE_TTL,
                'result_count' => count($result),
            ]);
        } catch (\Exception $e) {
            Log::warning('Cache save failed for discover filterTest', ['error' => $e->getMessage()]);
        }

        return $result;
    }
    public function filter(array $genres, array $languages): object
    {
        // 1. Filter di Laravel dulu sebelum kirim ke Flask
        $query = Movie::select([
            'tmdb_movie_id',
            'popularity',
            'rating',
            'rating_count',
            'original_language',
        ])->with(['genreVector']);

        // Filter bahasa
        if (!empty($languages)) {
            $query->whereIn('original_language', $languages);
        }

        // Filter genre via movie_genres
        if (!empty($genres)) {
            $query->whereHas('genres', function ($q) use ($genres) {
                $q->whereIn('map_genre_id', $genres);
            });
        }

        $movies = $query->get()->map(function ($movie) {
            return [
                'id'          => $movie->tmdb_movie_id,
                'popularity'  => $movie->popularity,
                'rating'      => $movie->rating,
                'rating_count' => $movie->rating_count,
                'vector'      => $movie->genreVector?->vector ?? '[]',
            ];
        })->toArray();

        Log::info('Movies setelah filter', [
            'total' => count($movies),
            'genres' => $genres,
            'languages' => $languages,
        ]);

        if (empty($movies)) {
            return collect();
        }

        // 2. Kirim ke Flask hanya data yang sudah difilter
        $rankedIds = $this->flaskService->getDiscover($movies);

        if (empty($rankedIds)) {
            return collect();
        }

        // 3. Query movie untuk tampilan
        return Movie::select([
            'tmdb_movie_id',
            'title',
            DB::raw('YEAR(release_date)as year'),
            'poster_path',
            'rating',
            'overview',
            'runtime',
        ])->whereIn('tmdb_movie_id', $rankedIds)
            ->orderByRaw('FIELD(tmdb_movie_id, ' . implode(',', $rankedIds) . ')')
            ->get();
    }
}
