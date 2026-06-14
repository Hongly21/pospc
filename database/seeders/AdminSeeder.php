<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create the Admin User using environment variables
        DB::table('users')->insert([
            'Username' => env('ADMIN_USERNAME', 'Admin'),
            'Email' => env('ADMIN_EMAIL'),
            'PasswordHash' => Hash::make(env('ADMIN_PASSWORD')),
            'RoleID' => '1',
            'Status' => 'Approved',
            'CreatedAt' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
