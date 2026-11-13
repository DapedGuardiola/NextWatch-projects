<?php

namespace App\Services\Api;

use App\Models\Movie;
use App\Models\Actor;
use App\Models\Genre;
use App\Models\Watchlist;
use App\Models\Favorite;
use App\Models\UserGenre;
use App\Models\userActors;
use App\Models\Comment;
use App\Models\userMovieInteracted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserActionService
{
    /**
     * Helper to resolve TMDB Movie ID and title from string identifier or numeric ID
     */
    protected function resolveMovie(string|int $identifier): ?Movie
    {
        if (is_numeric($identifier)) {
            return Movie::where('tmdb_movie_id', (int)$identifier)->first();
        }

        return Movie::where('title', 'LIKE', "%{$identifier}%")
            ->orderByRaw("CASE WHEN title = ? THEN 1 WHEN title LIKE ? THEN 2 ELSE 3 END", [$identifier, "{$identifier}%"])
            ->orderBy('popularity', 'desc')
            ->first();
    }

    /**
     * Helper to resolve Actor from name or numeric ID
     */
    protected function resolveActor(string|int $identifier): ?Actor
    {
        if (is_numeric($identifier)) {
            return Actor::where('tmdb_actor_id', (int)$identifier)->first();
        }

        return Actor::where('name', 'LIKE', "%{$identifier}%")
            ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", [$identifier, "{$identifier}%"])
            ->orderBy('popularity', 'desc')
            ->first();
    }

    /**
     * Helper to resolve Genre by name
     */
    protected function resolveGenre(string $genreName): ?Genre
    {
        return Genre::where('name', 'LIKE', "%{$genreName}%")->first();
    }

    // =========================================================================
    // 1. WATCHLIST ACTIONS
    // =========================================================================

    public function addToWatchlist(string|int $movieIdentifier, int $userId = 4): array
    {
        $movie = $this->resolveMovie($movieIdentifier);
        if (!$movie) {
            return [
                'status'  => 'error',
                'message' => "Film '{$movieIdentifier}' tidak ditemukan di database NextWatch.",
            ];
        }

        $watchlist = Watchlist::firstOrCreate([
            'user_id'  => $userId,
            'movie_id' => $movie->tmdb_movie_id,
        ]);

        try {
            userMovieInteracted::firstOrCreate([
                'user_id'       => $userId,
                'tmdb_movie_id' => $movie->tmdb_movie_id,
            ]);
            Cache::forget("user_movie_interacted_{$userId}");
        } catch (\Throwable $e) {
            // Optional analytics tracking, safe fallback
        }

        return [
            'status'     => 'success',
            'action'     => 'added_to_watchlist',
            'message'    => "Film '{$movie->title}' berhasil ditambahkan ke daftar tontonan (Watchlist).",
            'movie'      => [
                'id'     => $movie->tmdb_movie_id,
                'title'  => $movie->title,
                'rating' => (float)$movie->rating,
                'year'   => $movie->release_date ? substr($movie->release_date, 0, 4) : null,
            ],
            'user_id'    => $userId,
        ];
    }

    public function removeFromWatchlist(string|int $movieIdentifier, int $userId = 4): array
    {
        $movie = $this->resolveMovie($movieIdentifier);
        if (!$movie) {
            return [
                'status'  => 'error',
                'message' => "Film '{$movieIdentifier}' tidak ditemukan di database NextWatch.",
            ];
        }

        $deleted = Watchlist::where('user_id', $userId)
            ->where('movie_id', $movie->tmdb_movie_id)
            ->delete();

        return [
            'status'     => 'success',
            'action'     => 'removed_from_watchlist',
            'message'    => $deleted
                ? "Film '{$movie->title}' berhasil dihapus dari Watchlist."
                : "Film '{$movie->title}' memang tidak ada di dalam Watchlist Anda.",
            'movie'      => [
                'id'    => $movie->tmdb_movie_id,
                'title' => $movie->title,
            ],
            'user_id'    => $userId,
        ];
    }

    public function getUserWatchlist(int $userId = 4, int $limit = 20): array
    {
        $items = Watchlist::with('movie')
            ->where('user_id', $userId)
            ->latest('id')
            ->limit(min($limit, 50))
            ->get();

        return [
            'user_id'         => $userId,
            'watchlist_count' => $items->count(),
            'movies'          => $items->map(function ($item) {
                $m = $item->movie;
                return [
                    'id'           => $m?->tmdb_movie_id ?? $item->movie_id,
                    'title'        => $m?->title ?? 'Judul Tidak Ditemukan',
                    'rating'       => (float)($m?->rating ?? 0),
                    'release_date' => $m?->release_date,
                    'poster_url'   => $m?->poster_url,
                ];
            })->toArray(),
        ];
    }

    // =========================================================================
    // 2. FAVORITES ACTIONS
    // =========================================================================

    public function addToFavorites(string|int $movieIdentifier, int $userId = 4): array
    {
        $movie = $this->resolveMovie($movieIdentifier);
        if (!$movie) {
            return [
                'status'  => 'error',
                'message' => "Film '{$movieIdentifier}' tidak ditemukan di database NextWatch.",
            ];
        }

        Favorite::firstOrCreate([
            'user_id'  => $userId,
            'movie_id' => $movie->tmdb_movie_id,
        ], [
            'is_persona' => 0,
        ]);

        try {
            userMovieInteracted::firstOrCreate([
                'user_id'       => $userId,
                'tmdb_movie_id' => $movie->tmdb_movie_id,
            ]);
            Cache::forget("user_movie_interacted_{$userId}");
        } catch (\Throwable $e) {
            // Optional analytics tracking, safe fallback
        }

        return [
            'status'     => 'success',
            'action'     => 'added_to_favorites',
            'message'    => "Film '{$movie->title}' berhasil ditambahkan ke daftar Favorit.",
            'movie'      => [
                'id'     => $movie->tmdb_movie_id,
                'title'  => $movie->title,
                'rating' => (float)$movie->rating,
                'year'   => $movie->release_date ? substr($movie->release_date, 0, 4) : null,
            ],
            'user_id'    => $userId,
        ];
    }

    public function removeFromFavorites(string|int $movieIdentifier, int $userId = 4): array
    {
        $movie = $this->resolveMovie($movieIdentifier);
        if (!$movie) {
            return [
                'status'  => 'error',
                'message' => "Film '{$movieIdentifier}' tidak ditemukan di database NextWatch.",
            ];
        }

        $deleted = Favorite::where('user_id', $userId)
            ->where('movie_id', $movie->tmdb_movie_id)
            ->delete();

        return [
            'status'     => 'success',
            'action'     => 'removed_from_favorites',
            'message'    => $deleted
                ? "Film '{$movie->title}' berhasil dihapus dari Favorit."
                : "Film '{$movie->title}' memang tidak ada di dalam Favorit Anda.",
            'movie'      => [
                'id'    => $movie->tmdb_movie_id,
                'title' => $movie->title,
            ],
            'user_id'    => $userId,
        ];
    }

    public function getUserFavorites(int $userId = 4, int $limit = 20): array
    {
        $items = Favorite::with('movie')
            ->where('user_id', $userId)
            ->latest('id')
            ->limit(min($limit, 50))
            ->get();

        return [
            'user_id'         => $userId,
            'favorites_count' => $items->count(),
            'movies'          => $items->map(function ($item) {
                $m = $item->movie;
                return [
                    'id'           => $m?->tmdb_movie_id ?? $item->movie_id,
                    'title'        => $m?->title ?? 'Judul Tidak Ditemukan',
                    'rating'       => (float)($m?->rating ?? 0),
                    'release_date' => $m?->release_date,
                    'poster_url'   => $m?->poster_url,
                ];
            })->toArray(),
        ];
    }

    // =========================================================================
    // 3. GENRE PREFERENCE ACTIONS
    // =========================================================================

    public function addFavoriteGenre(string $genreName, int $userId = 4): array
    {
        $genre = $this->resolveGenre($genreName);
        if (!$genre) {
            return [
                'status'  => 'error',
                'message' => "Genre '{$genreName}' tidak dikenali di sistem.",
            ];
        }

        UserGenre::firstOrCreate([
            'user_id'  => $userId,
            'genre_id' => $genre->map_id,
        ], [
            'weight'   => 1,
        ]);

        return [
            'status'   => 'success',
            'action'   => 'added_favorite_genre',
            'message'  => "Genre '{$genre->name}' berhasil ditambahkan ke preferensi Anda.",
            'genre'    => [
                'id'   => $genre->map_id,
                'name' => $genre->name,
            ],
            'user_id'  => $userId,
        ];
    }

    public function removeFavoriteGenre(string $genreName, int $userId = 4): array
    {
        $genre = $this->resolveGenre($genreName);
        if (!$genre) {
            return [
                'status'  => 'error',
                'message' => "Genre '{$genreName}' tidak dikenali di sistem.",
            ];
        }

        $deleted = UserGenre::where('user_id', $userId)
            ->where('genre_id', $genre->map_id)
            ->delete();

        return [
            'status'   => 'success',
            'action'   => 'removed_favorite_genre',
            'message'  => $deleted
                ? "Genre '{$genre->name}' berhasil dihapus dari preferensi Anda."
                : "Genre '{$genre->name}' tidak terdaftar di preferensi Anda.",
            'genre'    => [
                'id'   => $genre->map_id,
                'name' => $genre->name,
            ],
            'user_id'  => $userId,
        ];
    }

    public function setFavoriteGenres(array $genreNames, int $userId = 4): array
    {
        $genres = Genre::where(function ($q) use ($genreNames) {
            foreach ($genreNames as $name) {
                $q->orWhere('name', 'LIKE', "%" . trim($name) . "%");
            }
        })->get();

        if ($genres->isEmpty()) {
            return [
                'status'  => 'error',
                'message' => 'Tidak ada genre yang cocok dengan input yang diberikan.',
            ];
        }

        // Reset preferensi genre user
        UserGenre::where('user_id', $userId)->delete();

        $saved = [];
        foreach ($genres as $g) {
            UserGenre::create([
                'user_id'  => $userId,
                'genre_id' => $g->map_id,
                'weight'   => 1,
            ]);
            $saved[] = $g->name;
        }

        return [
            'status'   => 'success',
            'action'   => 'set_favorite_genres',
            'message'  => 'Preferensi genre berhasil diperbarui.',
            'genres'   => $saved,
            'count'    => count($saved),
            'user_id'  => $userId,
        ];
    }

    public function getUserFavoriteGenres(int $userId = 4): array
    {
        $userGenres = UserGenre::with('genre')
            ->where('user_id', $userId)
            ->get();

        return [
            'user_id' => $userId,
            'count'   => $userGenres->count(),
            'genres'  => $userGenres->map(fn($ug) => [
                'id'   => $ug->genre?->map_id ?? $ug->genre_id,
                'name' => $ug->genre?->name ?? 'Genre Tidak Diketahui',
            ])->toArray(),
        ];
    }

    // =========================================================================
    // 4. ACTOR PREFERENCE ACTIONS
    // =========================================================================

    public function addFavoriteActor(string|int $actorIdentifier, int $userId = 4): array
    {
        $actor = $this->resolveActor($actorIdentifier);
        if (!$actor) {
            return [
                'status'  => 'error',
                'message' => "Aktor '{$actorIdentifier}' tidak ditemukan di database.",
            ];
        }

        userActors::firstOrCreate([
            'user_id'       => $userId,
            'tmdb_actor_id' => $actor->tmdb_actor_id,
        ]);

        return [
            'status'   => 'success',
            'action'   => 'added_favorite_actor',
            'message'  => "Aktor '{$actor->name}' berhasil ditambahkan ke daftar aktor favorit Anda.",
            'actor'    => [
                'id'   => $actor->tmdb_actor_id,
                'name' => $actor->name,
            ],
            'user_id'  => $userId,
        ];
    }

    public function removeFavoriteActor(string|int $actorIdentifier, int $userId = 4): array
    {
        $actor = $this->resolveActor($actorIdentifier);
        if (!$actor) {
            return [
                'status'  => 'error',
                'message' => "Aktor '{$actorIdentifier}' tidak ditemukan di database.",
            ];
        }

        $deleted = userActors::where('user_id', $userId)
            ->where('tmdb_actor_id', $actor->tmdb_actor_id)
            ->delete();

        return [
            'status'   => 'success',
            'action'   => 'removed_favorite_actor',
            'message'  => $deleted
                ? "Aktor '{$actor->name}' berhasil dihapus dari daftar aktor favorit."
                : "Aktor '{$actor->name}' memang tidak ada di daftar favorit Anda.",
            'actor'    => [
                'id'   => $actor->tmdb_actor_id,
                'name' => $actor->name,
            ],
            'user_id'  => $userId,
        ];
    }

    public function getUserFavoriteActors(int $userId = 4): array
    {
        $records = userActors::where('user_id', $userId)->get();
        $actorIds = $records->pluck('tmdb_actor_id')->toArray();
        $actors = Actor::whereIn('tmdb_actor_id', $actorIds)->get();

        return [
            'user_id' => $userId,
            'count'   => $actors->count(),
            'actors'  => $actors->map(fn($a) => [
                'id'         => $a->tmdb_actor_id,
                'name'       => $a->name,
                'popularity' => (float)$a->popularity,
                'image_url'  => $a->image_url,
            ])->toArray(),
        ];
    }

    // =========================================================================
    // 5. COMMENT ACTIONS
    // =========================================================================

    public function postMovieComment(string|int $movieIdentifier, string $content, int $userId = 4): array
    {
        $movie = $this->resolveMovie($movieIdentifier);
        if (!$movie) {
            return [
                'status'  => 'error',
                'message' => "Film '{$movieIdentifier}' tidak ditemukan di database.",
            ];
        }

        $comment = Comment::create([
            'user_id'  => $userId,
            'movie_id' => $movie->tmdb_movie_id,
            'content'  => trim($content),
        ]);

        return [
            'status'   => 'success',
            'action'   => 'posted_comment',
            'message'  => "Komentar berhasil dipublikasikan untuk film '{$movie->title}'.",
            'comment'  => [
                'id'         => $comment->id,
                'movie_id'   => $movie->tmdb_movie_id,
                'movie_title'=> $movie->title,
                'content'    => $comment->content,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            ],
            'user_id'  => $userId,
        ];
    }

    public function deleteMovieComment(int $commentId, int $userId = 4): array
    {
        $comment = Comment::where('id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if (!$comment) {
            return [
                'status'  => 'error',
                'message' => "Komentar #{$commentId} tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.",
            ];
        }

        $comment->delete();

        return [
            'status'   => 'success',
            'action'   => 'deleted_comment',
            'message'  => "Komentar #{$commentId} berhasil dihapus.",
            'user_id'  => $userId,
        ];
    }
}
