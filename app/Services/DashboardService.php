<?php

namespace App\Services;

class DashboardService
{
    public function getMovie()
    {
        $movies = collect([
            ['title' => 'Extraction',   'poster_path' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=500'],
            ['title' => 'The Dark Knight', 'poster_path' => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=500'],
            ['title' => 'Interstellar', 'poster_path' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=500'],
            ['title' => 'John Wick',    'poster_path' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=500'],
            ['title' => 'Avengers',     'poster_path' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?w=500'],
            ['title' => 'Inception',    'poster_path' => 'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=500'],
            ['title' => 'Mad Max',      'poster_path' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500'],
            ['title' => 'Dune',         'poster_path' => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=500'],
        ]);
        return $movies;
    }
}
