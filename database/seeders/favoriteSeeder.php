<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class favoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingMovies = DB::table('movies')->pluck('tmdb_movie_id')->toArray();
        $favorites = [
            ['user_id' => 2, 'tmdb_movie_id' => 19995],
            ['user_id' => 2, 'tmdb_movie_id' => 24428],
            ['user_id' => 2, 'tmdb_movie_id' => 37724],
        ];

        $validFavorites = array_filter($favorites, fn($fav) => in_array($fav['tmdb_movie_id'], $existingMovies));

        if (!empty($validFavorites)) {
            DB::table('favorite')->insert($validFavorites);
        }
    }
}
