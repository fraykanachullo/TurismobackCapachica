<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles si no existen
        $roles = ['superadmin', 'emprendedor', 'turista'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Verificar si el usuario ya existe, si no lo crea
        $user = User::firstOrCreate([
            'email' => 'fkanachullo12@gmail.com', // El correo con el que se quiere loguear
        ], [
            'name' => 'Super Admin', // Nombre del usuario
            'password' => Hash::make('fraykana10'), // Contraseña para el superadmin
        ]);

        // Asignar el rol de superadmin al usuario
        $user->assignRole('superadmin');
    }
}
