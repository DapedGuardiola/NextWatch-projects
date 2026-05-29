<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\FlaskService;
use App\Models\LogActivityModel;

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
        public function getBasePersonalization($user_id){
        
    }
    public function getMainContent($user_id) {
        $userLog = LogActivityModel::where('user_id', $user_id)->get();
        $userPersona = $this->getBasePersonalization($user_id);
        $minClick = 4;
        $minFav = 1;
        $minWatchlist = 1;
    
    }

}
