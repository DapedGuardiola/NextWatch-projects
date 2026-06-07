<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Table('normalized_movie')]
#[Fillable(['tmdb_movie_id','n_rating','n_popularity','n_rating_count'])]
class NormalizedMovieData extends Model
{
    public function movie(){
        return $this->belongsTo(Movie::class,'tmdb_movie_id','tmdb_movie_id');
    }
}
