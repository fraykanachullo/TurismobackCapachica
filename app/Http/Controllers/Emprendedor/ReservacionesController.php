<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservacionesController extends Controller
{
    /**
     * Mostrar todas las reservaciones relacionadas con los servicios del emprendedor autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        // Obtener las reservaciones de los servicios cuya empresa pertenece al emprendedor autenticado
        $reservations = Reservation::whereHas('service.company', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['user', 'service.company'])->latest()->get();

        return response()->json($reservations);
    }

    /**
     * Actualizar el estado de una reservación (confirmed, cancelled, completed).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation = Reservation::with('service.company')->findOrFail($id);

        // Verificar que la reserva pertenezca a un servicio de una empresa del emprendedor
        if ($reservation->service->company->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $reservation->status = $request->status;
        $reservation->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'reservation' => $reservation,
        ]);
    }

    /**
     * (Opcional) Mostrar detalles de una reserva específica.
     */
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'service.company'])->findOrFail($id);

        if ($reservation->service->company->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return response()->json($reservation);
    }
}
