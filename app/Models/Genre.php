<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('genres')]
#[Fillable(['map_id', 'name'])]
class Genre extends Model
{
    public function movies()
    {
        return $this->hasMany(MovieGenre::class, 'map_genre_id', 'map_id');
    }
}
