<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FlaskService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DetailService
{
    protected FlaskService $flaskService;
    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }
    public function getSelectedMovie(int $id)
    {
        $movie = Movie::selectRaw('movies.*, YEAR(release_date) as year')
            ->where('tmdb_movie_id', $id)->first();
        return $movie ? $movie->toArray() : [];
    }
    public function filterSimilar(int $movieId): object
    {
        $data = Redis::get("movie_similar_{$movieId}");
        $similar_Ids = $data ? json_decode($data, true) : [];
        $movie_datas = [];
        foreach ($similar_Ids as $movie_id) {
            $movie_datas[] = Cache::remember("movie_detail_{$movie_id}", 7600, function () use ($movie_id) {
                $movie = Movie::selectRaw('movies.*, YEAR(release_date) as year')
                    ->where('tmdb_movie_id', $movie_id)->first();
                return $movie ? $movie->toArray() : null;  // null bukan [] agar array_filter bisa hapus
            });
        }
        $movie_datas = array_filter($movie_datas);
        $similarMovies = Movie::hydrate($movie_datas);
        $similarMovies->load([
            'genres:tmdb_movie_id,map_genre_id',
            'actors:tmdb_actor_id',
            'directors:tmdb_director_id'
        ]);
        return $similarMovies;
    }
}
