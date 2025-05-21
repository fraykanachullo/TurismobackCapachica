<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function overview()
    {
        $userId = Auth::id();

        return response()->json([
            'upcoming_reservations' => Reservation::where('user_id', $userId)
                                                  ->where('reservation_date', '>=', now())
                                                  ->count(),
            'favorite_count'        => Auth::user()->favorites()->count(),
            'recent_reviews'        => Auth::user()->reviews()->latest()->limit(5)->get(),
            'recommended_services'  => Service::with('category','zone')->latest()->limit(5)->get(),
        ]);
    }
}
