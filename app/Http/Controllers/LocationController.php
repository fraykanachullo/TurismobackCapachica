<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    
    public function index(): JsonResponse
    {
        $comunidades = Location::with([
                'companies',                 // Relación empresa
                'companies.services',       // Servicios del emprendedor
                'companies.promotions',     // Promociones del emprendedor
                'companies.user'            // Información del usuario (opcional)
            ])
            ->where('type', 'comunidad')
            ->where('estado', 'activa')
            ->get([
                'id',
                'name',
                'descripcion_corta',
                'descripcion_larga',
                'atractivos',
                'habitantes',
                'estado',
                'imagen',
                'galeria',
                'created_at',
                'updated_at',
            ]);

        return response()->json($comunidades);
    }

    public function show($id): JsonResponse
{
    $location = Location::with([
            'companies.services',
            'companies.promotions',
            'companies.user'
        ])
        ->where('estado', 'activa') // Solo devolver si está activa
        ->find($id);

    if (!$location) {
        return response()->json(['message' => 'Comunidad no encontrada'], 404);
    }

    return response()->json($location);
}

}