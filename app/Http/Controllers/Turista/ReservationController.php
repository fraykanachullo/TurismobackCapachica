<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::where('user_id', Auth::id())->with('service')->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'people_count' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'reservation_date' => $request->reservation_date,
            'people_count' => $request->people_count,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Reserva creada', 'reservation' => $reservation], 201);
    }

    public function show($id)
    {
        $reservation = Reservation::with('service')->findOrFail($id);
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        return $reservation;
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        $reservation->update($request->only('reservation_date', 'people_count'));
        return response()->json(['message' => 'Reserva actualizada', 'reservation' => $reservation]);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        $reservation->delete();
        return response()->noContent();
    }

    // CONFIRMACION DE PAGOS .
    
    public function pay(Request $request, $id)
    {
        // 1) Obtener la reserva del usuario autenticado
        $reserva = Reservation::where('id', $id)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();

        // 2) Solo si está en estado 'confirmed'
        if ($reserva->status !== 'confirmed') {
            return response()->json([
                'message' => 'Solo puedes pagar reservas confirmadas.'
            ], 422);
        }

        // 3) Marcar pago
        $reserva->paid_at = Carbon::now();
        $reserva->status  = 'completed';
        $reserva->save();

        return response()->json([
            'message'     => 'Reserva pagada y completada correctamente.',
            'reservation' => $reserva->load('service.company')
        ], 200);
    }
}
