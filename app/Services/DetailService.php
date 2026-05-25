<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FlaskService;
class DetailService
{
    protected $flaskService;
    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }

    public function filterSimilar(int $movieId): object
    {
        // 1. Filter di Laravel dulu sebelum kirim ke Flask
        $target = Movie::select([
        'tmdb_movie_id',
        ])
        ->with([
            'genres:map_genre_id',
            'genreVector:tmdb_movie_id,vector',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ])
        ->where('tmdb_movie_id', $movieId)
        ->first();

    $genres = $target->genres
        ->pluck('map_genre_id')
        ->toArray();

    $query = Movie::select([
            'tmdb_movie_id',
        ])
        ->with([
            'genreVector:tmdb_movie_id,vector',
            'normalizedData:tmdb_movie_id,n_rating,n_popularity,n_rating_count'
        ]);

    if (!empty($genres)) {
        $query->whereHas('genres', function ($q) use ($genres) {
            $q->whereIn('map_genre_id', $genres);
        });
    }

        $target_movie = $target->map(function ($movie) {
            return [
                'id'           => $movie->tmdb_movie_id,
                'vector'       => $movie->genreVector?->vector ?? '[]',          // ✓ Akses via relationship
            ];
        })->toArray();

        $movies = $query->get()->map(function ($movie) {
            return [
                'id'           => $movie->tmdb_movie_id,
                'popularity'   => $movie->normalizedData?->n_popularity ?? 0,    // ✓ Akses via relationship
                'rating'       => $movie->normalizedData?->n_rating ?? 0,        // ✓ Akses via relationship
                'rating_count' => $movie->normalizedData?->n_rating_count ?? 0,  // ✓ Akses via relationship
                'vector'       => $movie->genreVector?->vector ?? '[]',          // ✓ Akses via relationship
            ];
        })->toArray();

        // Log::info('Movies setelah filter', [
        //     'total' => count($movies),
        //     'genres' => $genres,
        //     'languages' => $languages,
        // ]);

        if (empty($movies)) {
            return collect();
        }

        // 2. Kirim ke Flask hanya data yang sudah difilter
        $rankedIds = $this->flaskService->getSimilar($target_movie, $movies);

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