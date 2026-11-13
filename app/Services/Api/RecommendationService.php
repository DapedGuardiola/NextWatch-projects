<?php

namespace App\Services\Api;

use App\Models\Movie;
use App\Services\FlaskService;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    public function __construct(
        protected FlaskService $flaskService
    ) {}

    /**
     * Get personalized recommendations using SAW (Simple Additive Weighting) algorithm
     */
    public function getPersonalizedSaw(?array $userVector = null, int $limit = 10): array
    {
        // Ambil kandidat film terpopuler untuk dirangking
        $candidateMovies = Movie::orderBy('popularity', 'desc')
            ->limit(100)
            ->pluck('tmdb_movie_id')
            ->toArray();

        $rankedIds = [];

        try {
            if (!empty($userVector)) {
                $rankedIds = $this->flaskService->getDiscoverTest($userVector, $candidateMovies);
            } else {
                $rankedIds = $this->flaskService->getDiscover($candidateMovies);
            }
        } catch (\Throwable $e) {
            Log::warning("Flask SAW Service unavailable: " . $e->getMessage());
            // Fallback: urutkan berdasarkan popularity dan rating
            $rankedIds = $candidateMovies;
        }

        $topIds = array_slice($rankedIds, 0, min($limit, 30));
        $movies = Movie::whereIn('tmdb_movie_id', $topIds)
            ->with(['genres.genre'])
            ->get()
            ->sortBy(function ($m) use ($topIds) {
                return array_search($m->tmdb_movie_id, $topIds);
            })
            ->values();

        return [
            'algorithm'       => 'SAW (Simple Additive Weighting)',
            'count'           => $movies->count(),
            'recommendations' => $movies->map(fn($m) => [
                'id'           => $m->tmdb_movie_id,
                'title'        => $m->title,
                'rating'       => (float)$m->rating,
                'popularity'   => (float)$m->popularity,
                'release_date' => $m->release_date,
                'poster_url'   => $m->poster_url,
                'genres'       => $m->genres->map(fn($g) => $g->genre?->name)->filter()->values()->all(),
            ])->toArray()
        ];
    }

    /**
     * Get all-time best movies using EDAS (Evaluation based on Distance from Average Solution) algorithm
     */
    public function getAllTimeBestEdas(?string $genre = null, int $limit = 10): array
    {
        $query = Movie::where('rating', '>=', 7.0);

        if (!empty($genre)) {
            $query->whereHas('genres.genre', function ($q) use ($genre) {
                $q->where('name', 'LIKE', "%{$genre}%");
            });
        }

        $candidateMovies = $query->orderBy('popularity', 'desc')
            ->limit(100)
            ->pluck('tmdb_movie_id')
            ->toArray();

        $rankedIds = [];

        try {
            if (!empty($genre)) {
                $rankedIds = $this->flaskService->getBestMoviesByGenre($candidateMovies, $genre);
            } else {
                $rankedIds = $this->flaskService->getAllTimeBest($candidateMovies);
            }
        } catch (\Throwable $e) {
            Log::warning("Flask EDAS Service unavailable: " . $e->getMessage());
            $rankedIds = $candidateMovies;
        }

        $topIds = array_slice($rankedIds, 0, min($limit, 30));
        $movies = Movie::whereIn('tmdb_movie_id', $topIds)
            ->with(['genres.genre'])
            ->get()
            ->sortBy(function ($m) use ($topIds) {
                return array_search($m->tmdb_movie_id, $topIds);
            })
            ->values();

        return [
            'algorithm'       => 'EDAS (Evaluation based on Distance from Average Solution)',
            'genre_filter'    => $genre,
            'count'           => $movies->count(),
            'recommendations' => $movies->map(fn($m) => [
                'id'           => $m->tmdb_movie_id,
                'title'        => $m->title,
                'rating'       => (float)$m->rating,
                'popularity'   => (float)$m->popularity,
                'release_date' => $m->release_date,
                'poster_url'   => $m->poster_url,
                'genres'       => $m->genres->map(fn($g) => $g->genre?->name)->filter()->values()->all(),
            ])->toArray()
        ];
    }
}

