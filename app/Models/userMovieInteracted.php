<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Table('user_movie_interacted')]
#[Fillable(['user_id','tmdb_movie_id'])]
class userMovieInteracted extends Model
{
    //
}
