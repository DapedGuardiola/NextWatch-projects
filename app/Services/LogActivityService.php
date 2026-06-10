<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Jobs\ReevalTriger;
use App\Models\LogActivityModel;
use App\Models\userMovieInteracted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LogActivityService
{
    public function comment(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'comment',
            'created_at' => now(),
            'updated_at'=> now(),
        ]);
        $this->counterReevalTrigger();
    }

    public function favorite(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'favorite',
            'updated_at'=> now(),
            'created_at' => now(),
        ]);
        $this->counterReevalTrigger();
    }

    public function watchlist(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'watchlist',
            'updated_at'=> now(),
            'created_at' => now(),
        ]);
        $this->counterReevalTrigger();
    }

    public function click(Request $request)
    {
        $user = Auth::user();
        LogActivityModel::insert([
            'tmdb_movie_id' => $request->tmdb_movie_id,
            'user_id' => $user->id,
            'type' => 'click',
            'updated_at'=> now(),
            'created_at' => now(),
        ]);
        $this->counterReevalTrigger();
    }

    public function search(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'search',
            'updated_at'=> now(),
            'created_at' => now(),
        ]);
        $this->counterReevalTrigger();
    }

    public function watchTrailer($movie_id)
    {
        $user_id = Auth::user()->id;
        LogActivityModel::insert([
            'tmdb_movie_id' => $movie_id,
            'user_id' => $user_id,
            'type' => 'watch_trailer',
            'updated_at'=> now(),
            'created_at' => now(),
        ]);
        $interacted = userMovieInteracted::firstOrCreate(['user_id'=>$user_id, 'tmdb_movie_id'=>$movie_id]);
        if($interacted){
            Cache::forget("user_movie_interacted_{$user_id}");
        }
        $this->counterReevalTrigger();
    }

    public function counterReevalTrigger(){
        $user = Auth::user();
        $userLogIds = LogActivityModel::where(['user_id'=>$user->id,'is_evaluated'=>false])->get()->pluck('id')->toArray();
        if(count($userLogIds)>=5){
            ReevalTriger::dispatch($user->id,$userLogIds);
        }
    }
}
