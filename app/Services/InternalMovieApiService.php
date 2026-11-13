<?php

namespace App\Services;

use App\Services\Api\MovieService;
use App\Services\Api\ActorService;
use App\Services\Api\DirectorService;
use App\Services\Api\RecommendationService;

/**
 * Facade / Backward Compatibility Adapter for Internal API Services
 */
class InternalMovieApiService
{
    public function __construct(
        protected MovieService $movieService,
        protected ActorService $actorService,
        protected DirectorService $directorService,
        protected RecommendationService $recommendationService
    ) {}

    public function searchMovies(array $params): array
    {
        return $this->movieService->searchMovies($params);
    }

    public function getMovieDetail(string|int $identifier): ?array
    {
        return $this->movieService->getMovieDetail($identifier);
    }

    public function getTopRatedMovies(int $limit = 10, ?string $genre = null, float $minRating = 7.0): array
    {
        return $this->movieService->getTopRatedMovies($limit, $genre, $minRating);
    }

    public function compareMovies(string|int $identifier1, string|int $identifier2): ?array
    {
        return $this->movieService->compareMovies($identifier1, $identifier2);
    }

    public function getMoviesByDecade(int|string $decade, ?string $genre = null, float $minRating = 6.0, int $limit = 10): array
    {
        return $this->movieService->getMoviesByDecade($decade, $genre, $minRating, $limit);
    }

    public function recommendByMood(string $mood, int $limit = 10): array
    {
        return $this->movieService->recommendByMood($mood, $limit);
    }

    public function getSimilarMovies(string|int $identifier, int $limit = 8): ?array
    {
        return $this->movieService->getSimilarMovies($identifier, $limit);
    }

    public function getMovieDiscussions(string|int $identifier, int $limit = 10): ?array
    {
        return $this->movieService->getMovieDiscussions($identifier, $limit);
    }

    public function findActorByName(string $name, int $limit = 5): array
    {
        return $this->actorService->findActorByName($name, $limit);
    }

    public function getMoviesByActor(string|int $actorIdentifier, int $limit = 10): ?array
    {
        return $this->actorService->getMoviesByActor($actorIdentifier, $limit);
    }

    public function getSimilarActors(string|int $actorIdentifier, int $limit = 5): ?array
    {
        return $this->actorService->getSimilarActors($actorIdentifier, $limit);
    }

    public function findDirectorByName(string $name, int $limit = 5): array
    {
        return $this->directorService->findDirectorByName($name, $limit);
    }

    public function getMoviesByDirector(string|int $directorIdentifier, int $limit = 10): ?array
    {
        return $this->directorService->getMoviesByDirector($directorIdentifier, $limit);
    }
}
