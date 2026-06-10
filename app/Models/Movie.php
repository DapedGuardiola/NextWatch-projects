<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable([
    'tmdb_movie_id',
    'title',
    'overview',
    'poster_path',
    'popularity',
    'release_date',
    'tagline',
    'rating',
    'rating_count',
    'original_language',
    'backdrop_path',
])]
class Movie extends Model
{
    protected $primaryKey = 'tmdb_movie_id';
    protected $appends = ['poster_url'];
    public $incrementing = false;

    public function genres()
    {
        return $this->hasMany(MovieGenre::class, 'tmdb_movie_id', 'tmdb_movie_id');
    }

    protected function posterUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => 'https://image.tmdb.org/t/p/original/' . $this->poster_path
        );
    }
    protected function backgroundUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => 'https://image.tmdb.org/t/p/original/' . $this->backdrop_path
        );
    }

    public function genreVector()
    {
        return $this->hasOne(\App\Models\MoviesGenreVector::class, 'tmdb_movie_id', 'tmdb_movie_id');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'movie_id', 'tmdb_movie_id')
            ->whereNull('reply_id')
            ->with([
                'user',
                'replies.user',
                'taggedUser'
            ])
            ->latest();
    }

    public function normalizedData()
    {
        return $this->hasOne(NormalizedMovieData::class, 'tmdb_movie_id', 'tmdb_movie_id');
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class, 'movie_id', 'tmdb_movie_id');
    }

    public function directors()
    {
        return $this->belongsToMany(
            Director::class,
            'movie_directors',
            'tmdb_movie_id',
            'tmdb_director_id',
            'tmdb_movie_id',
            'tmdb_director_id'
        );
    }

    public function actors()
    {
        return $this->belongsToMany(
            Actor::class,
            'movie_actors',
            'tmdb_movie_id',
            'tmdb_actor_id',
            'tmdb_movie_id',
            'tmdb_actor_id'
        );
    }
    
    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'movie_id', 'tmdb_movie_id');
    }
    public function collection()
    {
        return $this->hasOne(CollectionModel::class,'tmdb_collection_id','tmdb_collection_id');
    }
}
