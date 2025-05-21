<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Reservation;

class CheckoutController extends Controller
{
    /**
     * Transforma el carrito en reservas “pending”
     * Requiere middleware 'auth:sanctum' + 'role:turista' y sesión web activa
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'cart'             => 'required|array|min:1',
            'cart.*.id'        => 'required|exists:services,id',
            'cart.*.quantity'  => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $reservations = [];

        foreach ($data['cart'] as $item) {
            $service = Service::findOrFail($item['id']);

            // Verificar cupo
            $occupied = Reservation::where('service_id', $service->id)
                          ->where('reservation_date', $data['reservation_date'])
                          ->sum('people_count');

            if ($occupied + $item['quantity'] > $service->capacity) {
                return response()->json([
                    'message' => "No hay disponibilidad para {$service->title} en {$data['reservation_date']}."
                ], 422);
            }

            $reservations[] = Reservation::create([
                'user_id'          => $user->id,
                'service_id'       => $service->id,
                'reservation_date' => $data['reservation_date'],
                'people_count'     => $item['quantity'],
                'total_amount'     => $service->price * $item['quantity'],
                'status'           => 'pending',
            ]);
        }

        // Limpiar carrito
       // $request->session()->forget('cart');

        return response()->json([
            'message'      => 'Reservas creadas con éxito.',
            'reservations' => $reservations,
        ], 201);
    }
}
