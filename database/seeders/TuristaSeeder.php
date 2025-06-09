<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TuristaSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de que exista el rol 'turista'
        // Role::firstOrCreate(['name' => 'turista']); // si no lo generó RolesSeeder

        // Creamos 10 usuarios con rol 'turista'
        User::factory()
            ->count(10)
            ->create()
            ->each(function (User $user) {
                // Fuerza datos únicos de prueba
                $user->email    = 'turista_' . Str::random(5) . '@ejemplo.com';
                $user->name     = 'Turista ' . Str::random(4);
                $user->password = Hash::make('password'); // Clave genérica
                $user->estado   = 'activo';
                $user->save();

                // Asigna el rol de turista
                $user->assignRole('turista');
            });
    }
}
