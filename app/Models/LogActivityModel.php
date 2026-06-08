<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('user_log_activity')]
#[Fillable(['tmdb_movie_id','user_id','type','is_evaluated','created_at'])]
class LogActivityModel extends Model
{
    public function movie(){
        return $this->belongsTo(Movie::class,'tmdb_movie_id','tmdb_movie_id');
    }
}
