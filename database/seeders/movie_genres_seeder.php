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
        $path = base_path('data/processed/updated2/new_genre_pivot.json');
        $json = file_get_contents($path);
        $movie_genres = json_decode($json, true);
        $existingMovies = DB::table('movies')->pluck('tmdb_movie_id')->toArray();
        $batchSize = 500;
        $data = [];

        foreach ($movie_genres as $movie_genre) {
            if (!in_array($movie_genre["movie_id"], $existingMovies)) {
                continue;
            }
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
