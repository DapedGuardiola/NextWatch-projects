<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tmdb_movie_id', 'map_genre_id'])]
class MovieGenre extends Model
{
    protected $table = 'movie_genres';

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'map_genre_id', 'map_id');
    }
}