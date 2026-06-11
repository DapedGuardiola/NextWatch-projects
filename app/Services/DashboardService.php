<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\CollectionModel;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\FlaskService;
use App\Models\LogActivityModel;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection\distinct;
use App\Models\User;
use App\Models\UserGenre;
use App\Models\Actor;
use App\Models\Watchlist;
use App\Models\UserRecommendation;

class DashboardService
{
    protected $flaskService;
    protected $user_id;
    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
        $this->user_id = Auth::id();
    }
    public function getMovie()
    {
        $movies = Movie::select([
            'tmdb_movie_id',
            'title',
            DB::raw('YEAR(release_date)as year'),
            'rating',
            'overview',
            'runtime',
            'poster_path',
        ])->with('genres.genre:map_id,name')
            ->orderBy('rating', 'Desc')
            ->limit(10)
            ->get();

        log::info('Data Movie berhasil diambil', ['movies' => $movies]);


        return $movies;
    }

    public function getMovieFlask(): array
    {
        return Movie::select([
            'tmdb_movie_id',
            'popularity',
            'release_date',
            'rating',
            'rating_count',
            'runtime',
        ])->with('genres.genre:map_id,name')
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->tmdb_movie_id,
                    'popularity' => $movie->popularity,
                    'runtime'    => $movie->runtime,
                    'rating'     => $movie->rating,
                    'rating_count'     => $movie->rating_count,
                    'release_date' => $movie->release_date,
                    'genres'     => $movie->genres->pluck('genre.map_id')->filter()->values()->toArray(),
                ];
            })->toArray();
    }

    public function rankTopByGenre()
    {
        $raw = $this->getMovieFlask();
        $ranked_id = $this->flaskService->getRanked($raw);
        $byGenreMovies = Movie::select([
            'tmdb_movie_id',
            'title',
            DB::raw('YEAR(release_date)as year'),
            'rating',
            'overview',
            'runtime',
            'poster_path',
        ])->with('genres.genre:map_id,name')
            ->orderBy('rating', 'Desc')
            ->limit(10)
            ->whereIn('tmdb_movie_id', $ranked_id)
            ->get();
        return $byGenreMovies;
    }

    public function getPopularMovie()
    {
        $popular = Movie::orderBy('popularity', 'desc')->first();
        return $popular;
    }

    public function getMainContent(int $user_id)
    {
        class_exists(\App\Models\Movie::class);
        $user_id = $this->user_id;
        // Di Controller, sebelum Cache::remember
        Log::info('READING KEY: ' . "user_rec_movie_{$user_id}");
        $cacheData = Cache::remember("user_rec_movie_{$user_id}", now()->addDays(7), function () use ($user_id) {
            Log::info('CACHE MISS - HIT DB for user: ' . $user_id);
            return [
                'ids' => UserRecommendation::where('user_id', $user_id)->pluck('tmdb_movie_id')->toArray(),
                'cached_at' => now()->toDateTimeString(),
            ];
        });

        $recommended_ids = $cacheData['ids'];
        Log::info('GOT IDS count: ' . count($recommended_ids) . ' cached_at: ' . $cacheData['cached_at']);

        $finalMoviesArray = [];
        foreach ($recommended_ids as $id) {
            $finalMoviesArray[] = Cache::remember("movie_detail_{$id}", now()->addDays(7), function () use ($id) {
                $movie = Movie::selectRaw('movies.*, YEAR(release_date) as year')
                    ->where('tmdb_movie_id', $id)->first();
                return $movie ? $movie->toArray() : null;
            });
        }
        $cleanArrays = array_filter($finalMoviesArray);
        $finalMovies = Movie::hydrate($cleanArrays);
        $finalMovies->load([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id'
        ]);
        $topOne = $finalMovies->first();
        $forYou = $finalMovies->skip(1)->take(9)->values();
        $others = $finalMovies->skip(10)->values();
        $topByGenre = $finalMovies->groupBy(function ($movie) {
            // data_get akan membaca $movie->genres indeks ke-0, lalu mengambil 'map_genre_id' 
            // secara aman, baik berbentuk array ['map_genre_id' => 1] maupun objek $genre->map_genre_id
            return data_get($movie, 'genres.0.map_genre_id');
        })->map(function ($group) {
            return collect($group)->first();
        })
            ->filter(function ($value, $key) {
                return $key !== '' && !is_null($key);
            })
            ->values()
            ->unique('tmdb_movie_id');
        $actor_ids = Cache::remember("user_rec_actor_{$user_id}", 7600, function () use ($finalMovies) {
            return $finalMovies->flatMap(function ($movie) {
                return $movie->actors ? $movie->actors->pluck('tmdb_actor_id') : [];
            })
                ->countBy()
                ->sortDesc()
                ->take(12)
                ->keys()
                ->toArray();
        });

        $actorsArray = [];
        foreach ($actor_ids as $id) {
            $actorsArray[] = Cache::remember("actor_detail_{$id}", 7600, function () use ($id) {
                $actor = Actor::where('tmdb_actor_id', $id)->first();
                return $actor ? $actor->toArray() : null;
            });
        }
        $cleanActorArrays = array_filter($actorsArray);
        // KUNCI JAWABANNYA DI SINI:
        // Kita ubah array murni tadi kembali menjadi Objek Model Movie asli Laravel
        $actors = Actor::hydrate($cleanActorArrays);

        $collection_ids = Cache::remember("user_rec_collection_{$user_id}", 7600, function () use ($finalMovies) {
            return $finalMovies
                ->whereNotNull('tmdb_collection_id')
                ->unique('tmdb_collection_id')
                ->take(7)
                ->pluck('tmdb_collection_id')->toArray();
        });
        $collectionsArray = [];
        foreach ($collection_ids as $id) {
            $collectionsArray[] = Cache::remember("collection_detail_{$id}", 7600, function () use ($id) {
                $collection =  CollectionModel::where('tmdb_collection_id', $id)->first();
                return $collection ? $collection->toArray() : null;
            });
        }
        $cleanCollection = array_filter($collectionsArray);
        $collections = CollectionModel::hydrate($cleanCollection);
        $user_genre_id = Cache::remember("user_genre_main_{$user_id}", 7600, function () use ($user_id) {
            return UserGenre::where('user_id', $user_id)->get()->pluck('genre_id')->toArray();
        });
        $upcomming_ids = Cache::remember("user_upcomming_{$user_id}", 7600, function () use ($user_genre_id) {
            return Movie::where('status', 'upcoming')
                ->whereHas('genres', function ($query) use ($user_genre_id) {
                    $query->whereIn('map_genre_id', $user_genre_id);
                })
                ->orderBy('popularity', 'desc')
                ->take(10)
                ->get()->pluck('tmdb_movie_id')->toArray();
        });
        $upcommingArray = [];
        foreach ($upcomming_ids as $id) {
            $upcommingArray[] = Cache::remember("movie_detail_{$id}", now()->addDays(7), function () use ($id) {
                $movie = Movie::selectRaw('movies.*, YEAR(release_date) as year')
                    ->where('tmdb_movie_id', $id)->first();
                return $movie ? $movie->toArray() : null;
            });
        }
        $upcomming = Movie::hydrate($upcommingArray);
        $upcomming->load([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id'
        ]);
        return ['topOne' => $topOne, 'forYou' => $forYou, 'actors' => $actors, 'collections' => $collections, 'others' => $others, 'upcomming' => $upcomming];
    }

    public function getWatchlist(int $user_id)
    {
        return Movie::whereHas('watchlists', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->take(5)
            ->get();
    }

    // $userGenre = UserGenre::where('user_id', $user_id)->get()->pluck('genre_id')->toArray();
    // $upcomming = Movie::where('status', 'upcoming')
    //     ->whereHas('genres', function ($query) use ($userGenre) {
    //         $query->whereIn('map_genre_id', $userGenre);
    //     })
    //     ->with('genres:tmdb_movie_id,map_genre_id')
    //     ->orderBy('popularity', 'desc')
    //     ->take(10)
    //     ->get();

    // public function getBasePersonalization($user_id)
    // {
    //     $movie = 'a';
    //     return $movie;
    // }

    // public function count_log_activity($userLog)
    // {
    //     $userClickCount = $userLog->where('type', 'click')->count();
    //     $userFavoriteCount = $userLog->where('type', 'favorite')->count();
    //     $userClickedMovieCount =  $userLog
    //         ->unique('tmdb_movie_id')
    //         ->count();
    //     if (
    //         $userClickCount > 3
    //         && $userFavoriteCount > 1
    //         && $userClickedMovieCount > 3
    //     ) {
    //         User::where('id', $this->user_id)->update(['persona_ready', true]);
    //     }
    // }
    // public function getBasicPreference()
    // {
    //     $persona = Favorite::where(['user_id' => $this->user_id, 'is_persona' => true]);
    // }
}
