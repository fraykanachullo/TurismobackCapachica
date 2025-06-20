<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class BookingController extends Controller
{
    // GET /api/turista/bookings
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('items.service','items.promotion')
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    // GET /api/turista/bookings/{id}
    public function show($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with('items.service','items.promotion')
            ->findOrFail($id);

        return response()->json($booking);
    }

    // POST /api/turista/bookings/{id}/pay
    public function pay($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Sólo se puede pagar un booking confirmado'], 422);
        }

        $booking->update([
            'status'  => 'completed',
            'paid_at' => now(),
        ]);

        return response()->json($booking->load('items.service','items.promotion'));
    }
}
