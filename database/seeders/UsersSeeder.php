<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin NextWatch',
                'email' => 'tester@nextwatch.com',
                'is_personalized' => false,
                'email_verified_at' => null,
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'avatar' => null,
                'gender' => null,
                'dob' => null,
                'bio' => null,
                'phone' => null,
                'password_changed_at' => null,
                'role' => 'user',
            ],
            [
                'name' => 'David',
                'email' => 'david@nextwatch.com',
                'is_personalized' => true,
                'email_verified_at' => null,
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'avatar' => null,
                'gender' => null,
                'dob' => null,
                'bio' => null,
                'phone' => null,
                'password_changed_at' => null,
                'role' => 'user',
            ]
        ]);
        DB::table('favorites')->insert([
            [
                'user_id' => 2,
                'movie_id' => 19995,
                'is_persona' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'movie_id' => 285,
                'is_persona' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'movie_id' => 49026,
                'is_persona' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        DB::table('watchlists')->insert([
            [
                'user_id' => 2,
                'movie_id' => 19995,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'movie_id' => 285,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'movie_id' => 49026,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
