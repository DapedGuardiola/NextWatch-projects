<?php

namespace App\Services\Api;

use App\Models\Director;

class DirectorService
{
    /**
     * Find director profile by name
     */
    public function findDirectorByName(string $name, int $limit = 5): array
    {
        $directors = Director::where('name', 'LIKE', "%{$name}%")
            ->with(['movies' => function ($q) {
                $q->orderBy('popularity', 'desc')->limit(5);
            }])
            ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", [$name, "{$name}%"])
            ->orderBy('popularity', 'desc')
            ->limit(min($limit, 20))
            ->get();

        return $directors->map(function ($director) {
            return [
                'tmdb_director_id' => $director->tmdb_director_id,
                'name'             => $director->name,
                'place_of_birth'   => $director->place_of_birth,
                'birthday'         => $director->birthday,
                'popularity'       => (float)$director->popularity,
                'biography'        => $director->biography ? substr($director->biography, 0, 250) . '...' : null,
                'known_for_movies' => $director->movies->map(fn($m) => [
                    'id'     => $m->tmdb_movie_id,
                    'title'  => $m->title,
                    'rating' => (float)$m->rating,
                    'year'   => $m->release_date ? substr($m->release_date, 0, 4) : null,
                ])->values()->all(),
            ];
        })->toArray();
    }

    /**
     * Get all movies directed by a specific director (by ID or name)
     */
    public function getMoviesByDirector(string|int $directorIdentifier, int $limit = 10): ?array
    {
        $query = Director::query();
        if (is_numeric($directorIdentifier)) {
            $director = $query->where('tmdb_director_id', (int)$directorIdentifier)->first();
        } else {
            $director = $query->where('name', 'LIKE', "%{$directorIdentifier}%")
                             ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", [$directorIdentifier, "{$directorIdentifier}%"])
                             ->orderBy('popularity', 'desc')
                             ->first();
        }

        if (!$director) {
            return null;
        }

        $movies = $director->movies()
            ->select('movies.tmdb_movie_id', 'movies.title', 'movies.rating', 'movies.popularity', 'movies.release_date', 'movies.overview', 'movies.poster_path')
            ->orderBy('movies.popularity', 'desc')
            ->limit(min($limit, 30))
            ->get();

        return [
            'director' => [
                'id'         => $director->tmdb_director_id,
                'name'       => $director->name,
                'popularity' => (float)$director->popularity,
            ],
            'movies_count' => $movies->count(),
            'movies'       => $movies->map(fn($m) => [
                'id'           => $m->tmdb_movie_id,
                'title'        => $m->title,
                'rating'       => (float)$m->rating,
                'popularity'   => (float)$m->popularity,
                'release_date' => $m->release_date,
                'poster_url'   => $m->poster_url,
                'overview'     => $m->overview ? substr($m->overview, 0, 150) . '...' : null,
            ])->toArray()
        ];
    }
}

