<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genres;
use Illuminate\Support\Facades\DB;

class genres_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/genres.json');
        $json = file_get_contents($path);
        $genres = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($genres as $genre) {
            $data[] = [
                'map_id' => $genre["id"],
                'name' => $genre["name"],
            ];
            if (count($data) === $batchSize) {
                DB::table("genres")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("genres")->insert($data);
        }
    }
}