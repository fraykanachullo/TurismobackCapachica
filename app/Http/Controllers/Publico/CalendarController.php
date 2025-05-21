<?php
// app/Http/Controllers/Publico/CalendarController.php
namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Devuelve las fechas bloqueadas (reservation_date) para un servicio.
     */
    public function occupiedDates($serviceId)
    {
        $dates = Reservation::where('service_id', $serviceId)
                    ->where('status', 'confirmed')        // o los estados que quieras bloquear
                    ->pluck('reservation_date')           // <— así, no “date”
                    ->unique()
                    ->values();

        return response()->json($dates);
    }
}
