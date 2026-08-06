<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'aldmic'],
            [
                'name' => 'Aldmic',
                'email' => 'aldmic@example.com',
                'password' => Hash::make('123abc123'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
