<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Table('movie_actors')]
#[Fillable(['tmdb_movie_id','tmdb_actor_id'])]
class MovieActor extends Model
{
    public function movies(){
        return $this->belongsTo(Movie::class,'tmdb_movie_id','tmdb_movie_id');
    }
}
