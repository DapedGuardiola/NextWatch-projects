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
        $path = base_path("data/processed/final/actors.json");
        $json = file_get_contents($path);
        $actors = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($actors as $actor) {
            $data[] = [
                'tmdb_actor_id' => $actor['id'],
                'name'          => $actor['name'],
                'image_path'    => $actor['image_path'] ?? null,
                'place_of_birth' => $actor['place_of_birth'] ?? null,
                'popularity'    => $actor['popularity'] ?? null,
                'biography'     => $actor['biography'] ?? null,
                'birthday'      => $actor['birthday'] ?? null,
                'deathday'      => $actor['deathday'] ?? null,
                'gender'        => $actor['gender'] ?? null,
            ];
            if (count($data) === $batchSize) {
                DB::table("actors")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("actors")->insert($data);
        }
    }
}
