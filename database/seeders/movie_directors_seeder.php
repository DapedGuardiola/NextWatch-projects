<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class movie_directors_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/updated2/final_director_pivot.json');
        $json = file_get_contents($path);
        $movie_directors = json_decode($json, true);
        $existingMovies = DB::table('movies')->pluck('tmdb_movie_id')->toArray();
        $batchSize = 500;
        $data = [];

        foreach ($movie_directors as $movie_director) {
            if (!in_array($movie_director["movie_id"], $existingMovies)) {
                continue;
            }
            $data[] = [
                'tmdb_movie_id' => $movie_director["movie_id"],
                'tmdb_director_id' => $movie_director["director_id"],
            ];
            if (count($data) === $batchSize) {
                DB::table("movie_directors")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("movie_directors")->insert($data);
        }
    }
}
