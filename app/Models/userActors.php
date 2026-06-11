<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Table('user_actors')]
#[Fillable(['user_id','tmdb_actor_id','type'])]
class userActors extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
