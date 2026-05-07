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
    }
}