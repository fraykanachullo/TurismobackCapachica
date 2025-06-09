<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Creamos o recuperamos dos usuarios de prueba
        $admin = User::firstOrCreate(
            ['email' => 'admin@seed.com'],
            [
                'name'              => 'Admin Seed',
                'password'          => Hash::make('password'),
                'foto'              => null,
                'estado'            => 'activo',
                'google_id'         => null,
                'email_verified_at' => now(),
            ]
        );

        $tourist = User::firstOrCreate(
            ['email' => 'tourist@seed.com'],
            [
                'name'              => 'Tourist Seed',
                'password'          => Hash::make('password'),
                'foto'              => null,
                'estado'            => 'activo',
                'google_id'         => null,
                'email_verified_at' => now(),
            ]
        );

        // 2. Ahora sí podemos insertar mensajes sin romper la FK
        Message::firstOrCreate([
            'sender_id'   => $admin->id,
            'receiver_id' => $tourist->id,
            'company_id'  => 1,
            'message'     => 'Hola, ¿podrías brindarme más información sobre tus servicios?',
        ], [
            'read'        => false,
        ]);

        Message::firstOrCreate([
            'sender_id'   => $tourist->id,
            'receiver_id' => $admin->id,
            'company_id'  => 1,
            'message'     => 'Claro, ¿qué quieres saber exactamente?',
        ], [
            'read'        => true,
        ]);
    }
}
