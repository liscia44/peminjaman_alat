<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'username'   => 'admin',
            'password'   => Hash::make('admin321'),
            'level'      => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}