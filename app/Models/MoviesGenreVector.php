<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoviesGenreVector extends Model
{
    protected $table = 'movie_genre_vector';
    protected $primaryKey = 'tmdb_movie_id';
    public $incrementing = false;
}
