<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'reply_id',
        'tagged_user_id',
        'content',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'tmdb_movie_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'reply_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_id')
            ->with('user')
            ->oldest();
    }

    public function taggedUser()
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }
}