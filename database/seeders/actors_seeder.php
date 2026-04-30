<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class actors_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path("/data/processed/actors.json");
        $json = file_get_contents($path);
        $actors = json_decode($json,true);
        $batchSize = 500;
        $data = [];
        
        foreach($actors as $actor){
            $data[] = [
                'tmdb_actor_id' => $actor["id"],
                'name' => $actor["name"],
                'image_path' => $actor["image_path"],
            ];
            if(count($data) === $batchSize){
                DB::table("actors")->insert($data);
                $data = [];
            }
        }
        if(!empty($data)){
            DB::table("actors")->insert($data);
        }
    }
}
