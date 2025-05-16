<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Promotion;
use App\Models\Company;
use App\Models\User;

class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar que exista un usuario para la empresa
        $user = User::firstOrCreate([
            'email' => 'empresa@demo.com',
        ], [
            'name' => 'Empresa Demo',
            'password' => bcrypt('password'),
        ]);

        // Crear o recuperar empresa con user_id = $user->id
        $company = Company::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'business_name' => 'Empresa Demo',
            'service_type' => 'turismo',
            'contact_email' => 'contacto@empresa.com',
            'ruc' => '20481234567',
            'status' => 'aprobada',
        ]);

        // Ahora sí crea los paquetes asociados a esta empresa
        Package::firstOrCreate([
            'company_id' => $company->id,
            'title' => 'Combo Aventura',
            'description' => 'Paquete combinado de kayak y hospedaje',
            'price' => 299.99,
            'status' => 'active',
        ]);

        Promotion::firstOrCreate([
            'company_id' => $company->id,
            'title' => 'Descuento Verano',
            'description' => '20% descuento en tours de verano',
            'discount_percentage' => 20,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Puedes agregar seeders para mensajes y blogs aquí si quieres
    }
}
