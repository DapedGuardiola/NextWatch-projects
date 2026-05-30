<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('collections')]
#[Fillable(['tmdb_collection_id','name','overview','poster_path','backdrop_path','original_language'])]
class CollectionModel extends Model
{
    public function movies(){
        return $this->hasMany(Movie::class,'tmdb_collection_id','tmdb_collection_id');
    }
    public function backdropUrl(): Attribute 
    {
        return Attribute::make(
            get: fn() => 'https://image.tmdb.org/t/p/original/' . $this->backdrop_path
        );
    }
}
