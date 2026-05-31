<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\FlaskService;
use App\Models\LogActivityModel;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection\distinct;
use App\Models\User;
use App\Models\Actor;
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
        $recommended_ids = UserRecommendation::where('user_id', $user_id)
            ->pluck('tmdb_movie_id');

        $movies = Movie::select('*', DB::raw('YEAR(release_date) as year'))
            ->whereIn('tmdb_movie_id', $recommended_ids)
            ->orderByRaw('FIELD(tmdb_movie_id, ' . $recommended_ids->implode(',') . ')')
            ->with([
                'genres:tmdb_movie_id,map_genre_id',
                'actors:tmdb_actor_id',
                'directors:tmdb_director_id'
            ])->get();
        $topOne = $movies->first();
        $forYou = $movies->skip(1)->take(9)->values();
        $others = $movies->skip(10)->values();
        $topByGenre = $movies->groupBy(function ($movie) {
            return $movie->genres->first()?->map_genre_id;
        })->map(function ($group) {
            return $group->first();
        })->values()
            ->unique('tmdb_movie_id'); // ← pastikan tidak duplikat
        $topActors = $movies->flatMap(function ($movie) {
            return $movie->actors->pluck('tmdb_actor_id');
        })
            ->countBy()
            ->sortDesc()
            ->take(12)
            ->keys();
        $actors = Actor::whereIn('tmdb_actor_id', $topActors)->get();
        $topCollections = $movies
            ->whereNotNull('tmdb_collection_id')
            ->unique('tmdb_collection_id')
            ->take(7)
            ->pluck('tmdb_collection_id');
        $collections = CollectionModel::whereIn('tmdb_collection_id', $topCollections)->get();
        return ['topOne' => $topOne, 'forYou' => $forYou, 'topByGenre' => $topByGenre, 'actors' => $actors,'collections'=>$collections,'others'=>$others];
    }

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
