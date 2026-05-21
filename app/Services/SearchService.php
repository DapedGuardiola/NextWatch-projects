<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Actor;

class SearchService
{
    public function live(string $query): array
    {
        if (strlen($query) < 2) {
            return ['movies' => [], 'actors' => []];
        }

        $movies = Movie::where('title', 'LIKE', "%{$query}%")
            ->select('tmdb_movie_id', 'title', 'poster_path', 'rating')
            ->limit(5)
            ->get()
            ->map(fn($m) => [
                'id'         => $m->tmdb_movie_id,
                'title'      => $m->title,
                'poster_url' => $m->poster_url,
                'rating'     => $m->rating,
                'url'        => route('movie.detail', $m->tmdb_movie_id),
            ]);

        $actors = Actor::where('name', 'LIKE', "%{$query}%")
            ->select('tmdb_actor_id', 'name', 'image_path')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id'        => $a->tmdb_actor_id,
                'name'      => $a->name,
                'image_url' => $a->image_url,
                'url'       => route('actor.detail', $a->tmdb_actor_id),
            ]);

        return [
            'movies' => $movies,
            'actors' => $actors,
        ];
    }

    public function search(string $query): array
    {
        $movies = Movie::where('title', 'LIKE', "%{$query}%")
            ->select('tmdb_movie_id', 'title', 'poster_path', 'rating', 'release_date','overview')
            ->limit(20)
            ->get();

        $actors = Actor::where('name', 'LIKE', "%{$query}%")
            ->select('tmdb_actor_id', 'name', 'image_path')
            ->limit(10)
            ->get();

        return compact('movies', 'actors');
    }
}