<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Company;
use App\Models\User;

class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Usuario de la empresa
        $user = User::firstOrCreate(
            ['email' => 'empresa@demo.com'],
            ['name' => 'Empresa Demo', 'password' => bcrypt('password')]
        );

        // 2) Empresa vinculada
        $company = Company::firstOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => 'Empresa Demo',
                'service_type'  => 'turismo',
                'contact_email' => 'contacto@empresa.com',
                'ruc'           => '20481234567',
                'status'        => 'aprobada',
            ]
        );

        // 3) Sembrar promociones de ejemplo
        Promotion::firstOrCreate(
            [
                'company_id'         => $company->id,
                'title'              => 'Descuento Verano',
            ],
            [
                'description'        => '20% descuento en tours de verano',
                'discount_percentage'=> 20,
                'start_date'         => now()->toDateString(),
                'end_date'           => now()->addMonth()->toDateString(),
                'status'             => 'active',
            ]
        );
    }
}
