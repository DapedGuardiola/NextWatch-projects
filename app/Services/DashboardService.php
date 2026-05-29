<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\FlaskService;
use App\Models\LogActivityModel;
use App\Models\Favorite;

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
        return
    }
    
    public function count_log_activity($userLog){
    $userClickCount = $userLog->where('type', 'click')->count();
    $userFavoriteCount = $userLog->where('type', 'favorite')->count();
    $userClickedMovieCount = $userLog->distinct('tmdb_movie_id')->count('tmdb_movie_id');
        if($userClickCount > 3 
        && $userFavoriteCount> 1
        && $userClickedMovieCount>3){
            Users::where('id',$this->user_id)->update(['persona_ready',true]);

        }
    }
    public function getBasicPreference(){
    $persona = Favorite::where(['user_id'=>$this->user_id,'is_persona'=>true]); 
    
    }
    public function getMainContent($user_id) {
        $user_persona_ready = Auth::user()->persona_ready;
        $userLog = LogActivityModel::where('user_id', $user_id)->get();
        if(!$user_persona_ready){
            $this->count_log_activity($userLog);
            $preference = $this->getBasicPreference();
            return $preference;
        }
        $preference = 
    }
}
