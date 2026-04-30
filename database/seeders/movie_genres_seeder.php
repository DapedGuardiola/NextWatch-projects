<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;
class movie_genres_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/movie_genres_pivot.json');
        $json = file_get_contents($path);
        $movie_genres = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($movie_genres as $movie_genre) {
            $data[] = [
                'tmdb_movie_id' => $movie_genre["movie_id"],
                'map_genre_id' => $movie_genre["genre_id"],
            ];
            if (count($data) === $batchSize) {
                DB::table("movie_genres")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("movie_genres")->insert($data);
        }
    }
}
