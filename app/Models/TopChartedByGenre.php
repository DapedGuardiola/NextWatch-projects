<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopChartedByGenre extends Model
{
    protected $table = 'top_charted_by_genre';
    
    protected $casts = [
        'movies_data' => 'json',
    ];
    
    protected $fillable = [
        'genre_name',
        'movies_data',
        'last_updated',
    ];
}
