<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero creamos roles y superadmin
        $this->call([
            RolesSeeder::class,
        ]);

        // Luego semillamos usuarios turista
        $this->call([
            TuristaSeeder::class,
        ]);

        // Después tus otros seeders
        $this->call([
            ExtraSeeder::class,
            MessageSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
