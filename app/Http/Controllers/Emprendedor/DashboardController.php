<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function overview()
    {
        $companyId = Auth::user()->company->id;

        return response()->json([
            'ventas_totales'    => Reservation::whereHas('service', fn($q) => $q->where('company_id', $companyId))->count(),
            'reseñas_recibidas' => Service::where('company_id', $companyId)->withCount('reviews')->get()->sum('reviews_count'),
            'visitas'           => 0, // Define cómo medir visitas, quizás logs o contador separado
        ]);
    }
}
