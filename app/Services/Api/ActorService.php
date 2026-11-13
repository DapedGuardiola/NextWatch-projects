<?php

namespace App\Services\Api;

use App\Models\Actor;
use App\Models\MovieActor;
use Illuminate\Support\Facades\DB;

class ActorService
{
    /**
     * Find actor profile by name (WHERE LIKE) with their top known movies
     */
    public function findActorByName(string $name, int $limit = 5): array
    {
        $actors = Actor::where('name', 'LIKE', "%{$name}%")
            ->with(['actormovies.movies'])
            ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", [$name, "{$name}%"])
            ->orderBy('popularity', 'desc')
            ->limit(min($limit, 20))
            ->get();

        return $actors->map(function ($actor) {
            $movies = $actor->actormovies
                ->map(fn($am) => $am->movies)
                ->filter()
                ->sortByDesc('popularity')
                ->take(5);

            return [
                'tmdb_actor_id'    => $actor->tmdb_actor_id,
                'name'             => $actor->name,
                'place_of_birth'   => $actor->place_of_birth,
                'birthday'         => $actor->birthday,
                'popularity'       => (float)$actor->popularity,
                'image_url'        => $actor->image_url,
                'biography'        => $actor->biography ? substr($actor->biography, 0, 250) . '...' : null,
                'known_for_movies' => $movies->map(fn($m) => [
                    'id'     => $m->tmdb_movie_id,
                    'title'  => $m->title,
                    'rating' => (float)$m->rating,
                    'year'   => $m->release_date ? substr($m->release_date, 0, 4) : null,
                ])->values()->all(),
            ];
        })->toArray();
    }

    /**
     * Get all movies starring a specific actor (by ID or name)
     */
    public function getMoviesByActor(string|int $actorIdentifier, int $limit = 10): ?array
    {
        $query = Actor::with(['actormovies.movies']);
        if (is_numeric($actorIdentifier)) {
            $actor = $query->where('tmdb_actor_id', (int)$actorIdentifier)->first();
        } else {
            $actor = $query->where('name', 'LIKE', "%{$actorIdentifier}%")
                           ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", [$actorIdentifier, "{$actorIdentifier}%"])
                           ->orderBy('popularity', 'desc')
                           ->first();
        }

        if (!$actor) {
            return null;
        }

        $movies = $actor->actormovies
            ->map(fn($am) => $am->movies)
            ->filter()
            ->sortByDesc('popularity')
            ->take(min($limit, 30));

        return [
            'actor' => [
                'id'         => $actor->tmdb_actor_id,
                'name'       => $actor->name,
                'popularity' => (float)$actor->popularity,
                'image_url'  => $actor->image_url,
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
            ])->values()->toArray()
        ];
    }

    /**
     * Get similar actors based on shared movie genres
     */
    public function getSimilarActors(string|int $actorIdentifier, int $limit = 5): ?array
    {
        $query = Actor::with(['actormovies.movies.genres.genre']);
        if (is_numeric($actorIdentifier)) {
            $actor = $query->where('tmdb_actor_id', (int)$actorIdentifier)->first();
        } else {
            $actor = $query->where('name', 'LIKE', "%{$actorIdentifier}%")
                           ->orderBy('popularity', 'desc')
                           ->first();
        }

        if (!$actor) {
            return null;
        }

        $genreIds = [];
        if ($actor->actormovies) {
            foreach ($actor->actormovies as $actorMovie) {
                if ($actorMovie->movies && $actorMovie->movies->genres) {
                    foreach ($actorMovie->movies->genres as $genre) {
                        $genreIds[] = $genre->map_genre_id;
                    }
                }
            }
        }

        $genreIds = array_unique(array_filter($genreIds));

        if (empty($genreIds)) {
            $similarActors = Actor::where('tmdb_actor_id', '!=', $actor->tmdb_actor_id)
                ->orderBy('popularity', 'desc')
                ->limit($limit)
                ->get();
        } else {
            $similarActorRecords = DB::table('movie_actors')
                ->join('movie_genres', 'movie_actors.tmdb_movie_id', '=', 'movie_genres.tmdb_movie_id')
                ->whereIn('movie_genres.map_genre_id', $genreIds)
                ->where('movie_actors.tmdb_actor_id', '!=', $actor->tmdb_actor_id)
                ->select('movie_actors.tmdb_actor_id', DB::raw('COUNT(DISTINCT movie_genres.map_genre_id) as shared_genres'))
                ->groupBy('movie_actors.tmdb_actor_id')
                ->orderByDesc('shared_genres')
                ->limit($limit)
                ->get();

            $ids = $similarActorRecords->pluck('tmdb_actor_id')->toArray();
            $actorsMap = Actor::whereIn('tmdb_actor_id', $ids)->get()->keyBy('tmdb_actor_id');

            $result = [];
            foreach ($similarActorRecords as $rec) {
                $itemActor = $actorsMap->get($rec->tmdb_actor_id);
                if ($itemActor) {
                    $result[] = [
                        'id'            => $itemActor->tmdb_actor_id,
                        'name'          => $itemActor->name,
                        'popularity'    => (float)$itemActor->popularity,
                        'image_url'     => $itemActor->image_url,
                        'shared_genres' => (int)$rec->shared_genres,
                    ];
                }
            }

            return [
                'reference_actor' => [
                    'id'   => $actor->tmdb_actor_id,
                    'name' => $actor->name,
                ],
                'count'           => count($result),
                'similar_actors'  => $result,
            ];
        }

        return [
            'reference_actor' => [
                'id'   => $actor->tmdb_actor_id,
                'name' => $actor->name,
            ],
            'count'           => $similarActors->count(),
            'similar_actors'  => $similarActors->map(fn($a) => [
                'id'         => $a->tmdb_actor_id,
                'name'       => $a->name,
                'popularity' => (float)$a->popularity,
                'image_url'  => $a->image_url,
            ])->toArray(),
        ];
    }
}

