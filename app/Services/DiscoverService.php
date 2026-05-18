<?php

namespace App\Services;

use App\Helpers\OriginalLanguageHelper;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\NormalizedMovieData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscoverService
{
    protected FlaskService $flaskService;

    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
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
        // 1. Filter di Laravel dulu sebelum kirim ke Flask
        $query = Movie::select([
            'tmdb_movie_id',
            'original_language',
        ])->with([
            'genreVector:tmdb_movie_id,vector',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ]);

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

        $movies = $query->get()->map(function ($movie) {
            return [
                'id'           => $movie->tmdb_movie_id,
                'popularity'   => $movie->normalizedData?->n_popularity ?? 0,    // ✓ Akses via relationship
                'rating'       => $movie->normalizedData?->n_rating ?? 0,        // ✓ Akses via relationship
                'rating_count' => $movie->normalizedData?->n_rating_count ?? 0,  // ✓ Akses via relationship
                'vector'       => $movie->genreVector?->vector ?? '[]',          // ✓ Akses via relationship
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
        $rankedIds = $this->flaskService->getDiscoverTest($user_vector, $movies);

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
