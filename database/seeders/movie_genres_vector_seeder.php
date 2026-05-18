<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class movie_genres_vector_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path("/data/processed/fixed_movie_vector.json");
        $json = file_get_contents($path);
        $vectors =json_decode($json, true);
        $data = [];
        $batchSize = 500;

        foreach($vectors as $vector){
            $data [] = [
                'tmdb_movie_id'=>$vector["movie_id"],
                'vector'=> json_encode($vector['vector']),
            ];
            if(count($data) === $batchSize){
                DB::table("movie_genre_vector")->insert($data);
                $data = [];
            }
        }
        if(!empty($data)){
            DB::table("movie_genre_vector")->insert($data);
        }

    }
}
