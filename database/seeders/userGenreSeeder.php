<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class userGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_genres')->insert([
            [
                'user_id' => 2,
                'genre_id' => 1,
            ],
            [
                'user_id' => 2,
                'genre_id' => 2,
            ],
            [
                'user_id' => 2,
                'genre_id' => 16,
            ],
        ]);
    }
}
