<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class normalizeMovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/normalized_movie.json');
        $json = file_get_contents($path);
        $movies = json_decode($json, true);
        $batch_size = 500;
        $data = [];

        foreach($movies as $m){
            $data [] = [
                'tmdb_movie_id' => $m["movie_id"],
                'n_rating'=> $m["n_rating"],
                'n_popularity'=> $m["n_popularity"],
                'n_rating_count'=> $m["n_rating_count"],
            ];

            if(count($data) === $batch_size){
                DB::table('normalized_movie')->insert($data);
                $data = [];
            }
        }
        if(!empty($data)){
            DB::table('normalized_movie')->insert($data);
        }
    }
}
