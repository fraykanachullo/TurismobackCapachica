<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Promotion;

class CheckoutController extends Controller
{
    // POST /api/checkout
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'cart'             => 'required|array|min:1',
            'cart.*.type'      => 'required|in:service,promotion',
            'cart.*.id'        => 'required|integer',
            'cart.*.quantity'  => 'required|integer|min:1',
        ]);

        $user    = Auth::user();
        $total   = 0;
        $booking = Booking::create([
            'user_id'          => $user->id,
            'reservation_date' => $data['reservation_date'],
            'total_amount'     => 0,
            'status'           => 'pending',
        ]);

        foreach ($data['cart'] as $item) {
            if ($item['type'] === 'service') {
                $svc = Service::findOrFail($item['id']);
                $pb  = $svc->price;
                $pa  = $pb;

                BookingItem::create([
                    'booking_id'   => $booking->id,
                    'type'         => 'service',
                    'service_id'   => $svc->id,
                    'promotion_id' => null,
                    'quantity'     => $item['quantity'],
                    'price_before' => $pb,
                    'price_after'  => $pa,
                ]);

                $total += $pa * $item['quantity'];

            } else {
                $promo = Promotion::active()->with('services')->findOrFail($item['id']);

                foreach ($promo->services as $svc) {
                    $pb = $svc->price;
                    $pa = round($pb * (1 - $promo->discount_percentage / 100), 2);

                    BookingItem::create([
                        'booking_id'   => $booking->id,
                        'type'         => 'promotion',
                        'service_id'   => $svc->id,
                        'promotion_id' => $promo->id,
                        'quantity'     => $item['quantity'],
                        'price_before' => $pb,
                        'price_after'  => $pa,
                    ]);

                    $total += $pa * $item['quantity'];
                }
            }
        }

        $booking->update(['total_amount' => $total]);

        return response()->json([
            'message' => 'Booking creado exitosamente.',
            'booking' => $booking->load('items'),
        ], 201);
    }
}
