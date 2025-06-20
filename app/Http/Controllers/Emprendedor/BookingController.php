<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class BookingController extends Controller
{
    // GET /api/emprendedor/bookings
    public function index()
    {
        $userId = Auth::id();

        $bookings = Booking::whereHas('items.service.company', fn($q) => $q->where('user_id', $userId))
            ->with(['items.service','items.promotion','user'])
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    // PUT /api/emprendedor/bookings/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json($booking->load('items.service','items.promotion','user'));
    }

     // GET /api/emprendedor/bookings/{booking}
     public function show($id)
     {
         $booking = Booking::with(['items.service','items.promotion','user'])
             ->findOrFail($id);
 
         // Verificamos que al menos uno de los items pertenezca a un servicio
         // de la empresa del emprendedor autenticado
         $owns = $booking->items
             ->where('type','service')
             ->pluck('service')
             ->filter(fn($svc) => $svc->company->user_id === Auth::id())
             ->isNotEmpty();
 
         if (! $owns) {
             return response()->json(['message'=>'No autorizado'], 403);
         }
 
         return response()->json($booking);
     }
}
