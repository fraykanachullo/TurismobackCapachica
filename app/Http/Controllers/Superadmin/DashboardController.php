<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Company;
use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview()
    {
        return response()->json([
            'total_users'        => User::count(),
            'pending_companies'  => Company::where('status','pendiente')->count(),
            'total_services'     => Service::count(),
            'total_reservations' => Reservation::count(),
            // … otras métricas …
        ]);
    }
}
