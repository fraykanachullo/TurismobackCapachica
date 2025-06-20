<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class PublicServiceController extends Controller
{
    // GET /api/servicios-publicos
    public function index(Request $request)
    {
        $q = Service::where('status', 'active')
            ->with(['category','zone','media','promotions'=>fn($q)=>$q->active(),'itineraries']);

        // filtros… (category_id, zone_id, price_min/max)

        return response()->json($q->get());
    }

    // GET /api/servicios-publicos/{service}
    public function show(Service $service)
    {
        $service->load(['category','zone','media','promotions'=>fn($q)=>$q->active(),'itineraries']);
        return response()->json($service);
    }
}
