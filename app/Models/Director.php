<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tmdb_director_id',
    'name',
    'image_path',
    'place_of_birth',
    'popularity',
    'biography',
    'birthday',
    'deathday',
    'gender',
])]
class Director extends Model
{
    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'movie_directors',
            'tmdb_director_id',
            'tmdb_movie_id',
            'tmdb_director_id',
            'tmdb_movie_id'
        );
    }
}
