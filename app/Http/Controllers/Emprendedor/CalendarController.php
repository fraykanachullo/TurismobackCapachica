<?php
// app/Http/Controllers/Emprendedor/CalendarController.php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:emprendedor');
    }

    /**
     * Devuelve las fechas ocupadas (reservation_date) de un servicio
     * para el emprendedor dueño del servicio.
     */
    public function occupiedDates(Service $service)
    {
        // Autorizar que el servicio pertenezca a la empresa autenticada
        if ($service->company_id !== Auth::user()->company->id) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }

        // Estados que bloquean la fecha
        $blockedStatuses = ['pre_reservation', 'booked', 'paid', 'confirmed'];

        $dates = $service->reservations()
                         ->whereIn('status', $blockedStatuses)
                         ->pluck('reservation_date')
                         ->unique()
                         ->values();

        return response()->json($dates, Response::HTTP_OK);
    }
}
