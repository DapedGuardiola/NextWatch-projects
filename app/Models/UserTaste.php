<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Table('user_tastes')]
#[Fillable([
    'user_id',
    'preferred_actors',
    'preferred_directors',
    'preferred_normalized_popularity',
    'preferred_normalized_rating',
    'preferred_era',
    'persona_version',
    'activity_since_last_eval',
    'persona_ready',
])]
class UserTaste extends Model
{
    protected $casts = [
        'preferred_actors' => 'array',
        'preferred_directors' => 'array',
        'preferred_era' => 'array',
        'preferred_normalized_popularity'=>'float',
        'preferred_normalized_rating'=>'float',
        'persona_ready' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
