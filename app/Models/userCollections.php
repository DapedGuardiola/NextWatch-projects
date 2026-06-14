<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('user_collections')]
#[Fillable(['user_id','tmdb_collection_id','type'])]
class userCollections extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
