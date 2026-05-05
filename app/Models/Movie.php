<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tmdb_movie_id', 'title', 'overview', 'poster_path',
    'popularity', 'release_date', 'tagline',
    'rating', 'rating_count', 'original_language'
])]
class Movie extends Model
{
    protected $primaryKey = 'tmdb_movie_id';
    public $incrementing = false;

    public function genres()
    {
        return $this->hasMany(MovieGenre::class, 'tmdb_movie_id', 'tmdb_movie_id');
    }
}