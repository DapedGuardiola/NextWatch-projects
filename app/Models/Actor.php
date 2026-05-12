<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\MovieActor;

#[Table('actors')]
#[Fillable(['tmdb_actor_id','name','image_path'])]
class Actor extends Model
{
    public function actormovies(){
        return $this->hasMany(MovieActor::class,'tmdb_actor_id','tmdb_actor_id');
    }
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => 'https://image.tmdb.org/t/p/w300/'. $this->image_path
        );
    }
}
