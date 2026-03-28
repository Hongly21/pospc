<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create the Admin User
        DB::table('users')->insert([
            'Username' => 'Boun Hongly',
            'Email' => 'hongly06082004@gmail.com',
            'PasswordHash' => Hash::make('11111111'),
            'RoleID' => '1',
            'Status' => 'Approved',
            'CreatedAt' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
