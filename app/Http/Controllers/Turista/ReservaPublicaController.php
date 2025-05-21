<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ReservaPublicaController extends Controller
{
    /**
     * Crear una nueva reserva para un servicio.
     */
    public function store(Request $request)
    {
        // Capturamos los datos validados en $data
        $data = $request->validate([
            'service_id'       => 'required|exists:services,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'people_count'     => 'required|integer|min:1',
        ]);

        $service = Service::findOrFail($data['service_id']);

        // Verificar disponibilidad
        $existing = Reservation::where('service_id', $service->id)
                        ->where('reservation_date', $data['reservation_date'])
                        ->sum('people_count');

        if ($existing + $data['people_count'] > $service->capacity) {
            return response()->json([
                'message' => 'No hay disponibilidad para la fecha seleccionada.'
            ], 422);
        }

        // Calcular el total
        $total = $service->price * $data['people_count'];

        $reservation = Reservation::create([
            'user_id'          => Auth::id(),
            'service_id'       => $service->id,
            'reservation_date' => $data['reservation_date'],
            'people_count'     => $data['people_count'],
            'total_amount'     => $total,
            'status'           => 'pending',
        ]);

        return response()->json([
            'message'     => 'Reserva creada exitosamente.',
            'reservation' => $reservation
        ], 201);
    }

    /**
     * Mostrar todas las reservas del turista autenticado.
     */
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with('service.company')
            ->latest()
            ->get();

        return response()->json($reservations);
    }
}
