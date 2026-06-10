<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class directors_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path("data/processed/final/directors.json");
        $json = file_get_contents($path);
        $directors = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($directors as $director) {
            $data[] = [
                'tmdb_director_id' => $director['tmdb_director_id'],
                'name'          => $director['name'],
                'image_path'    => $director['profile_path'] ?? null,
                'place_of_birth' => $director['place_of_birth'] ?? null,
                'popularity'    => $director['popularity'] ?? null,
                'biography'     => $director['biography'] ?? null,
                'birthday'      => $director['birthday'] ?? null,
                'deathday'      => $director['deathday'] ?? null,
                'gender'        => $director['gender'] ?? null,
            ];
            if (count($data) === $batchSize) {
                DB::table("directors")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("directors")->insert($data);
        }
    }
}
