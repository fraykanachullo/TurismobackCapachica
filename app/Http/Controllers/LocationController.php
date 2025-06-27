<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * GET /api/locations
     * Sólo devuelve comunidades activas.
     */
    public function index(): JsonResponse
    {
        $comunidades = Location::where('type', 'comunidad')
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
}
