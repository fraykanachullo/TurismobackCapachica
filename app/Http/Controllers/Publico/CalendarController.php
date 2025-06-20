<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    // GET /api/services/{service}/calendar
    public function occupiedDates(Service $service)
    {
        $blocked = ['pending','confirmed','completed'];
        $dates = $service->reservations()
                         ->whereIn('status', $blocked)
                         ->pluck('reservation_date')
                         ->unique()
                         ->values();
        return response()->json($dates, Response::HTTP_OK);
    }
}
