<?php

namespace App\Services;

use App\Models\Actor;
use Illuminate\Support\Facades\DB;

class ActorService
{
    /**
     * Mengambil data aktor beserta film yang dibintanginya
     */
    public function getActorMovies(int $id)
    {
        return Actor::with('actormovies.movies.genres.genre')
            ->where('tmdb_actor_id', $id)
            ->firstOrFail();
    }
    
    /**
     * Mengambil aktor serupa berdasarkan irisan genre film terbanyak
     */
    public function getSimilarActors($actor)
    {
        // 1. Kumpulkan semua ID genre dari film-film sang aktor
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
        
        $genreIds = array_unique($genreIds);

        if (empty($genreIds)) {
            return collect(); // Return kosong jika tidak ada data genre
        }

        // 2. Cari aktor lain yang filmnya beririsan dengan genre-genre di atas
        // Kita hitung skor kemiripan berdasarkan jumlah genre yang sama
        $similarActorIds = DB::table('movie_actors')
            ->join('movie_genres', 'movie_actors.tmdb_movie_id', '=', 'movie_genres.tmdb_movie_id')
            ->whereIn('movie_genres.map_genre_id', $genreIds)
            ->where('movie_actors.tmdb_actor_id', '!=', $actor->tmdb_actor_id) // Kecualikan aktor itu sendiri
            ->select('movie_actors.tmdb_actor_id', DB::raw('COUNT(DISTINCT movie_genres.map_genre_id) as shared_genres'))
            ->groupBy('movie_actors.tmdb_actor_id')
            ->orderBy('shared_genres', 'desc')
            ->limit(6)
            ->pluck('tmdb_actor_id');

        if ($similarActorIds->isEmpty()) {
            return collect();
        }
        
        // 3. Ambil data aktor sesuai urutan skor tertinggi
        $placeholders = implode(',', array_fill(0, count($similarActorIds), '?'));
        
        return Actor::whereIn('tmdb_actor_id', $similarActorIds)
            ->orderByRaw("FIELD(tmdb_actor_id, $placeholders)", $similarActorIds->toArray())
            ->get();
    }

    public function getActor()
    {
        return Actor::orderBy('id', 'desc')->take(15)->get();
    }
}