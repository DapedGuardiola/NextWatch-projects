<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\MovieActor;

class ActorService
{
    public function getActor()
    {
        return Actor::select(['tmdb_actor_id', 'name', 'image_path'])->limit(10)->get();
    }
    public function getActorMovies($actor_id){
        return Actor::with('actormovies.movies.genres.genre')->where('tmdb_actor_id',$actor_id)->first();
    }
}
