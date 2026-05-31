<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGenre extends Model
{
    use HasFactory;

    // Sesuai dengan migration temanmu (pakai 's')
    protected $table = 'user_genres'; 
    
    // Matikan timestamps karena di migration tidak ada $table->timestamps()
    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'genre_id',
        'weight' // Disesuaikan dengan nama kolom di migration
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'map_id');
    }
}