<?php

namespace App\Services\Api;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\Comment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovieService
{
    /**
     * Search movies with multiple criteria (query, genre, min_rating, release_year)
     */
    public function searchMovies(array $params): array
    {
        $queryBuilder = Movie::query();

        if (!empty($params['query'])) {
            $q = $params['query'];
            $queryBuilder->where(function ($sub) use ($q) {
                $sub->where('title', 'LIKE', "%{$q}%")
                    ->orWhere('overview', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($params['genre'])) {
            $genreName = $params['genre'];
            $queryBuilder->whereHas('genres.genre', function ($q) use ($genreName) {
                $q->where('name', 'LIKE', "%{$genreName}%");
            });
        }

        if (!empty($params['min_rating'])) {
            $queryBuilder->where('rating', '>=', (float)$params['min_rating']);
        }

        if (!empty($params['release_year'])) {
            $queryBuilder->whereYear('release_date', (int)$params['release_year']);
        }

        $limit = isset($params['limit']) ? min((int)$params['limit'], 30) : 10;

        $movies = $queryBuilder
            ->with(['genres.genre'])
            ->orderBy('popularity', 'desc')
            ->limit($limit)
            ->get();

        return $movies->map(function ($m) {
            return [
                'id'           => $m->tmdb_movie_id,
                'title'        => $m->title,
                'rating'       => (float)$m->rating,
                'popularity'   => (float)$m->popularity,
                'release_date' => $m->release_date,
                'poster_url'   => $m->poster_url,
                'overview'     => $m->overview ? substr($m->overview, 0, 150) . '...' : null,
                'genres'       => $m->genres->map(fn($g) => $g->genre?->name)->filter()->values()->all(),
            ];
        })->toArray();
    }

    /**
     * Get detailed movie information by TMDB ID or title
     */
    public function getMovieDetail(string|int $identifier): ?array
    {
        $query = Movie::with(['genres.genre', 'directors', 'actors']);

        if (is_numeric($identifier)) {
            $movie = $query->where('tmdb_movie_id', (int)$identifier)->first();
        } else {
            $movie = $query->where('title', 'LIKE', "%{$identifier}%")
                           ->orderByRaw("CASE WHEN title = ? THEN 1 WHEN title LIKE ? THEN 2 ELSE 3 END", [$identifier, "{$identifier}%"])
                           ->orderBy('popularity', 'desc')
                           ->first();
        }

        if (!$movie) {
            return null;
        }

        return [
            'id'                => $movie->tmdb_movie_id,
            'title'             => $movie->title,
            'tagline'           => $movie->tagline,
            'overview'          => $movie->overview,
            'rating'            => (float)$movie->rating,
            'rating_count'      => (int)$movie->rating_count,
            'popularity'        => (float)$movie->popularity,
            'release_date'      => $movie->release_date,
            'original_language' => $movie->original_language,
            'poster_url'        => $movie->poster_url,
            'background_url'    => $movie->background_url,
            'genres'            => $movie->genres->map(fn($g) => $g->genre?->name)->filter()->values()->all(),
            'directors'         => $movie->directors->map(fn($d) => [
                'id'         => $d->tmdb_director_id,
                'name'       => $d->name,
                'popularity' => (float)$d->popularity,
            ])->values()->all(),
            'top_cast'          => $movie->actors->sortByDesc('popularity')->take(8)->map(fn($a) => [
                'id'         => $a->tmdb_actor_id,
                'name'       => $a->name,
                'popularity' => (float)$a->popularity,
            ])->values()->all(),
        ];
    }

    /**
     * Get top-rated movies optionally filtered by genre
     */
    public function getTopRatedMovies(int $limit = 10, ?string $genre = null, float $minRating = 7.0): array
    {
        return $this->searchMovies([
            'min_rating' => $minRating,
            'genre'      => $genre,
            'limit'      => $limit
        ]);
    }

    /**
     * Compare two movies head-to-head (rating, popularity, genres, release date, cast)
     */
    public function compareMovies(string|int $identifier1, string|int $identifier2): ?array
    {
        $movie1 = $this->getMovieDetail($identifier1);
        $movie2 = $this->getMovieDetail($identifier2);

        if (!$movie1 || !$movie2) {
            return [
                'status'  => 'error',
                'message' => 'Salah satu atau kedua film tidak dapat ditemukan untuk dibandingkan.',
                'found_movie_1' => (bool)$movie1,
                'found_movie_2' => (bool)$movie2,
            ];
        }

        $sharedGenres = array_values(array_intersect($movie1['genres'] ?? [], $movie2['genres'] ?? []));

        $cast1Ids = array_column($movie1['top_cast'] ?? [], 'name', 'id');
        $cast2Ids = array_column($movie2['top_cast'] ?? [], 'name', 'id');
        $sharedCast = array_values(array_intersect($cast1Ids, $cast2Ids));

        $ratingDiff = round(abs($movie1['rating'] - $movie2['rating']), 2);
        if ($movie1['rating'] > $movie2['rating']) {
            $higherRated = $movie1['title'];
        } elseif ($movie2['rating'] > $movie1['rating']) {
            $higherRated = $movie2['title'];
        } else {
            $higherRated = 'Seimbang (Rating Sama)';
        }

        return [
            'movie_1' => [
                'id'           => $movie1['id'],
                'title'        => $movie1['title'],
                'rating'       => $movie1['rating'],
                'votes'        => $movie1['rating_count'],
                'popularity'   => $movie1['popularity'],
                'release_date' => $movie1['release_date'],
                'genres'       => $movie1['genres'],
                'directors'    => array_column($movie1['directors'], 'name'),
            ],
            'movie_2' => [
                'id'           => $movie2['id'],
                'title'        => $movie2['title'],
                'rating'       => $movie2['rating'],
                'votes'        => $movie2['rating_count'],
                'popularity'   => $movie2['popularity'],
                'release_date' => $movie2['release_date'],
                'genres'       => $movie2['genres'],
                'directors'    => array_column($movie2['directors'], 'name'),
            ],
            'comparison' => [
                'higher_rated'       => $higherRated,
                'rating_difference'  => $ratingDiff,
                'shared_genres'      => $sharedGenres,
                'shared_cast'        => $sharedCast,
            ]
        ];
    }

    /**
     * Get movies by decade / era (e.g. 1980s, 1990s, 2000s, 2010s, 2020s)
     */
    public function getMoviesByDecade(int|string $decade, ?string $genre = null, float $minRating = 6.0, int $limit = 10): array
    {
        $clean = preg_replace('/[^0-9]/', '', (string)$decade);
        $num = (int)$clean;

        if ($num < 100) {
            $startYear = ($num >= 20) ? (1900 + $num) : (2000 + $num);
            $startYear = (int)(floor($startYear / 10) * 10);
        } else {
            $startYear = (int)(floor($num / 10) * 10);
        }
        $endYear = $startYear + 9;

        $query = Movie::query()
            ->whereBetween('release_date', ["{$startYear}-01-01", "{$endYear}-12-31"])
            ->where('rating', '>=', $minRating);

        if (!empty($genre)) {
            $query->whereHas('genres.genre', function ($q) use ($genre) {
                $q->where('name', 'LIKE', "%{$genre}%");
            });
        }

        $movies = $query->with(['genres.genre'])
            ->orderBy('popularity', 'desc')
            ->limit(min($limit, 30))
            ->get();

        return [
            'decade'     => "{$startYear}s ({$startYear}-{$endYear})",
            'count'      => $movies->count(),
            'movies'     => $movies->map(fn($m) => [
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
     * Recommend movies based on mood / vibe
     */
    public function recommendByMood(string $mood, int $limit = 10): array
    {
        $moodNormalized = strtolower(trim($mood));

        $moodMap = [
            'happy'         => ['genres' => ['Comedy', 'Animation', 'Family'], 'min_rating' => 6.5],
            'feel_good'     => ['genres' => ['Comedy', 'Romance', 'Family'], 'min_rating' => 7.0],
            'ceria'         => ['genres' => ['Comedy', 'Animation'], 'min_rating' => 6.5],
            'lucu'          => ['genres' => ['Comedy'], 'min_rating' => 6.5],
            'adrenaline'    => ['genres' => ['Action', 'Thriller', 'Adventure'], 'min_rating' => 7.0],
            'tegang'        => ['genres' => ['Thriller', 'Action', 'Mystery'], 'min_rating' => 7.0],
            'seru'          => ['genres' => ['Action', 'Adventure', 'Sci-Fi'], 'min_rating' => 7.0],
            'mind_bending'  => ['genres' => ['Sci-Fi', 'Mystery', 'Thriller'], 'min_rating' => 7.5],
            'mikir'         => ['genres' => ['Sci-Fi', 'Mystery'], 'min_rating' => 7.5],
            'twist'         => ['genres' => ['Mystery', 'Thriller'], 'min_rating' => 7.5],
            'romantic'      => ['genres' => ['Romance', 'Drama'], 'min_rating' => 7.0],
            'baper'         => ['genres' => ['Romance', 'Drama'], 'min_rating' => 7.0],
            'cinta'         => ['genres' => ['Romance'], 'min_rating' => 6.5],
            'scary'         => ['genres' => ['Horror', 'Thriller'], 'min_rating' => 6.0],
            'horor'         => ['genres' => ['Horror'], 'min_rating' => 6.0],
            'merinding'     => ['genres' => ['Horror', 'Mystery'], 'min_rating' => 6.0],
            'inspirational' => ['genres' => ['Drama', 'History', 'Biography'], 'min_rating' => 7.5],
            'motivasi'      => ['genres' => ['Drama', 'History'], 'min_rating' => 7.5],
            'sedih'         => ['genres' => ['Drama'], 'min_rating' => 7.0],
        ];

        $matched = null;
        foreach ($moodMap as $key => $config) {
            if (str_contains($moodNormalized, $key)) {
                $matched = $config;
                break;
            }
        }

        if (!$matched) {
            $matched = ['genres' => ['Drama', 'Action', 'Comedy'], 'min_rating' => 7.0];
        }

        $genres = $matched['genres'];
        $minRating = $matched['min_rating'];

        $movies = Movie::where('rating', '>=', $minRating)
            ->whereHas('genres.genre', function ($q) use ($genres) {
                $q->whereIn('name', $genres);
            })
            ->with(['genres.genre'])
            ->orderBy('popularity', 'desc')
            ->limit(min($limit, 30))
            ->get();

        return [
            'mood'           => $mood,
            'target_genres'  => $genres,
            'recommendations'=> $movies->map(fn($m) => [
                'id'           => $m->tmdb_movie_id,
                'title'        => $m->title,
                'rating'       => (float)$m->rating,
                'popularity'   => (float)$m->popularity,
                'release_date' => $m->release_date,
                'poster_url'   => $m->poster_url,
                'overview'     => $m->overview ? substr($m->overview, 0, 140) . '...' : null,
                'genres'       => $m->genres->map(fn($g) => $g->genre?->name)->filter()->values()->all(),
            ])->toArray()
        ];
    }

    /**
     * Get similar movies based on content & shared genres
     */
    public function getSimilarMovies(string|int $identifier, int $limit = 8): ?array
    {
        $movieQuery = Movie::with('genres');
        if (is_numeric($identifier)) {
            $movie = $movieQuery->where('tmdb_movie_id', (int)$identifier)->first();
        } else {
            $movie = $movieQuery->where('title', 'LIKE', "%{$identifier}%")
                                ->orderByRaw("CASE WHEN title = ? THEN 1 WHEN title LIKE ? THEN 2 ELSE 3 END", [$identifier, "{$identifier}%"])
                                ->orderBy('popularity', 'desc')
                                ->first();
        }

        if (!$movie) {
            return null;
        }

        $genreIds = $movie->genres->pluck('map_genre_id')->filter()->unique()->toArray();

        if (empty($genreIds)) {
            $similarMovies = Movie::where('tmdb_movie_id', '!=', $movie->tmdb_movie_id)
                ->orderBy('popularity', 'desc')
                ->limit($limit)
                ->get();
        } else {
            $similarMovies = Movie::select('movies.*', DB::raw('COUNT(movie_genres.map_genre_id) as shared_genres_count'))
                ->join('movie_genres', 'movies.tmdb_movie_id', '=', 'movie_genres.tmdb_movie_id')
                ->whereIn('movie_genres.map_genre_id', $genreIds)
                ->where('movies.tmdb_movie_id', '!=', $movie->tmdb_movie_id)
                ->groupBy('movies.tmdb_movie_id')
                ->orderBy('shared_genres_count', 'desc')
                ->orderBy('movies.popularity', 'desc')
                ->limit($limit)
                ->with(['genres.genre'])
                ->get();
        }

        return [
            'reference_movie' => [
                'id'     => $movie->tmdb_movie_id,
                'title'  => $movie->title,
                'rating' => (float)$movie->rating,
            ],
            'similar_count'   => $similarMovies->count(),
            'movies'          => $similarMovies->map(fn($m) => [
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
     * Get community reviews & discussion comments for a movie
     */
    public function getMovieDiscussions(string|int $identifier, int $limit = 10): ?array
    {
        $movieQuery = Movie::query();
        if (is_numeric($identifier)) {
            $movie = $movieQuery->where('tmdb_movie_id', (int)$identifier)->first();
        } else {
            $movie = $movieQuery->where('title', 'LIKE', "%{$identifier}%")->orderBy('popularity', 'desc')->first();
        }

        if (!$movie) {
            return null;
        }

        $comments = Comment::with('user')
            ->where('movie_id', $movie->tmdb_movie_id)
            ->whereNull('reply_id')
            ->latest()
            ->limit(min($limit, 25))
            ->get();

        return [
            'movie' => [
                'id'    => $movie->tmdb_movie_id,
                'title' => $movie->title,
            ],
            'comments_count' => $comments->count(),
            'discussions'    => $comments->map(fn($c) => [
                'id'         => $c->id,
                'author'     => $c->user?->name ?? 'Anonymous',
                'comment'    => $c->content,
                'created_at' => $c->created_at?->format('Y-m-d H:i'),
            ])->toArray()
        ];
    }
}

