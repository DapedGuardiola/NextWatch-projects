<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            collection_seeder::class,
            genres_seeder::class,
            actors_seeder::class,
            movies_seeder::class,
            movie_genres_seeder::class,
            movie_actors_seeder::class,
            movie_genres_vector_seeder::class,
            UsersSeeder::class,
            normalizeMovieSeeder::class,
            userGenreSeeder::class,
            directors_seeder::class,
            movie_directors_seeder::class,
        ]);
    }
}
