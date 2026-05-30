<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Table('user_recommendations')]
#[Fillable(['user_id','tmdb_movie_id','type'])]
class UserRecommendation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
