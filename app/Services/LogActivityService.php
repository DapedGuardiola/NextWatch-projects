<?php

namespace App\Services;

use App\Models\LogActivityModel;

class LogActivityService
{
    public function comment(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'comment',
            'created_at' => now(),
        ]);
    }

    public function favorite(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'favorite',
            'created_at' => now(),
        ]);
    }

    public function watchlist(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'watchlist',
            'created_at' => now(),
        ]);
    }

    public function click(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'click',
            'created_at' => now(),
        ]);
    }

    public function search(array $data)
    {
        LogActivityModel::insert([
            'tmdb_movie_id' => $data['movie_id'],
            'user_id' => $data['user_id'],
            'type' => 'search',
            'created_at' => now(),
        ]);
    }
}
