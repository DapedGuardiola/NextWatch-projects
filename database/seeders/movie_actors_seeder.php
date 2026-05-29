<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class movie_actors_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/updated/fixed_actor_pivot.json');
        $json = file_get_contents($path);
        $movie_actors = json_decode($json, true);
        $existingMovies = DB::table('movies')->pluck('tmdb_movie_id')->toArray();
        $batchSize = 500;
        $data = [];

        foreach ($movie_actors as $movie_actor) {
            if (!in_array($movie_actor["movie_id"], $existingMovies)) {
                continue;
            }
            $data[] = [
                'tmdb_movie_id' => $movie_actor["movie_id"],
                'tmdb_actor_id' => $movie_actor["cast_id"],
            ];
            if (count($data) === $batchSize) {
                DB::table("movie_actors")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("movie_actors")->insert($data);
        }
    }
}
