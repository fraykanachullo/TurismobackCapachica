<?php
namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class CalendarController extends Controller
{
    public function occupiedDates($serviceId)
    {
        $dates = Reservation::where('service_id', $serviceId)
            ->whereIn('status', ['confirmed', 'paid'])
            ->pluck('date');

        return response()->json($dates);
    }
}
