<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // This calls the AdminSeeder we just fixed above
        $this->call(AdminSeeder::class);
    }
}
