<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::firstOrCreate([
            'sender_id' => 1,
            'receiver_id' => 2,
            'company_id' => 1,
            'message' => 'Hola, ¿podrías brindarme más información sobre tus servicios?',
            'read' => false,
        ]);

        Message::firstOrCreate([
            'sender_id' => 2,
            'receiver_id' => 1,
            'company_id' => 1,
            'message' => 'Claro, ¿qué quieres saber exactamente?',
            'read' => true,
        ]);

        // Agrega más mensajes si lo necesitas
    }
}
