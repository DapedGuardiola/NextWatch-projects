<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FlaskService;
class DetailService
{
    protected FlaskService $flaskService;
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
            'genres:tmdb_movie_id,map_genre_id',
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

        $target_movie = [
            'id' => $target->tmdb_movie_id,
            'movie_genre_vector' => json_decode(
                $target->genreVector?->vector ?? '[]',
                true
            )
        ];

        $movies = $query->get()->map(function ($movie) {
            return [
                'id'           => $movie->tmdb_movie_id,
                'popularity'   => $movie->normalizedData?->n_popularity ?? 0,    // ✓ Akses via relationship
                'rating'       => $movie->normalizedData?->n_rating ?? 0,        // ✓ Akses via relationship
                'rating_count' => $movie->normalizedData?->n_rating_count ?? 0,  // ✓ Akses via relationship
                'vector' => json_decode(
            $movie->genreVector?->vector ?? '[]',
            true
        )
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
        $similar_Ids = $this->flaskService->getSimilar(
        $target_movie,
        $movies
    );
        // dd($similar_Ids);

        // Log::info('Movies setelah filter', [
        //     'data' => $similar_Ids
        // ]);

        if (empty($similar_Ids)) {
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
        ])->whereIn('tmdb_movie_id', $similar_Ids)
            ->orderByRaw('FIELD(tmdb_movie_id, ' . implode(',', $similar_Ids) . ')')
            ->get();
    }
}