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

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_id')
            ->with('replies', 'user')
            ->oldest();
    }

    public function allRepliesFlat(): \Illuminate\Support\Collection
    {
        $flat = collect();

        foreach ($this->replies as $reply) {
            $flat->push($reply);
            $flat = $flat->merge($reply->allRepliesFlat());
        }

        return $flat;
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'reply_id')
            ->with('user');
    }

    public function taggedUser()
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommentLike::class);
    }
    
    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommentReport::class);
    }
    
    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->likes()->where('user_id', $userId)->exists();
    }
    
    public function isReportedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->reports()->where('user_id', $userId)->exists();
    }
}