<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGenre extends Model
{
    protected $table = 'user_genres';
    protected $fillable = [
        'user_id',
        'genre_id',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
