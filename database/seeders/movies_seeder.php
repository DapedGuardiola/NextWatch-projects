<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class movies_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path("data/processed/final/movies.json");
        $json = file_get_contents($path);
        $movies = json_decode($json, true);
        $batchSize = 500;
        $data = [];

        foreach ($movies as $movie) {
            $releaseDate = filled($movie['release_date'] ?? null)
                ? \Carbon\Carbon::parse($movie['release_date'])
                : null;

            $status = (
                !$releaseDate ||
                $releaseDate->isAfter(today())
            )
                ? 'upcoming'
                : 'released';

            $data[] = [
                'tmdb_movie_id' => $movie["tmdb_movie_id"],
                'title' => $movie["title"],
                'overview' => $movie["overview"],
                'poster_path' => $movie["poster_path"],
                'popularity' => $movie["popularity"],
                'release_date' => filled($movie['release_date'] ?? null)? $movie['release_date'] : null ,
                'runtime' => $movie["runtime"],
                'status' => $status,
                'tagline' => $movie["tagline"],
                'rating' => $movie["rating"],
                'rating_count' => $movie["rating_count"],
                'original_language' => $movie["original_language"],
                'tmdb_collection_id' => $movie["collection_id"],
                'trailer_key' => $movie["trailer_key"],
                'trailer_size' => $movie["trailer_size"],
            ];
            if (count($data) === $batchSize) {
                DB::table("movies")->insert($data);
                $data = [];
            }
        }
        if (!empty($data)) {
            DB::table("movies")->insert($data);
        }
    }
}
