<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class PublicServiceController extends Controller
{
    /**
     * Listar servicios públicos activos con filtros opcionales.
     */
    public function index(Request $request)
    {
        $q = Service::where('status', 'active')
                    ->with(['category','zone','media']);

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->category_id);
        }

        if ($request->filled('zone_id')) {
            $q->where('location_id', $request->zone_id);
        }

        if ($request->filled('price_min')) {
            $q->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $q->where('price', '<=', $request->price_max);
        }

        return response()->json($q->get());
    }

    /**
     * Mostrar detalle de un servicio público activo.
     */
    public function show($id)
    {
        $service = Service::with(['category','zone','media'])
                          ->where('status', 'active')
                          ->findOrFail($id);

        return response()->json($service);
    }
}
