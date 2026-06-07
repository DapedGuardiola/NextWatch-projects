<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class collection_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('data/processed/updated2/collections_updated.json');
        $json = file_get_contents($path);
        $collections = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($collections as $collection) {
            $data[] = [
                'tmdb_collection_id' => $collection["collection_id"],
                'name' => $collection["name"],
                'original_language' => $collection["original_language"],
                'overview' => $collection["overview"],
                'poster_path' => $collection["poster_path"],
                'backdrop_path' => $collection["backdrop_path"],
            ];
            if (count($data) === $batchSize) {
                DB::table("collections")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("collections")->insert($data);
        }
    }
}
