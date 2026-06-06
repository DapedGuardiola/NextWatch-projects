<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopChartedAllTime extends Model
{
    protected $table = 'top_charted_all_time';
    
    protected $casts = [
        'movies_data' => 'json',
    ];
    
    protected $fillable = [
        'movies_data',
        'last_updated',
    ];
}
