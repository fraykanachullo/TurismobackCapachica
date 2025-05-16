<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Reservation;
use App\Models\Service;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesBy()
    {
        return Reservation::select('services.category_id', DB::raw('count(*) as total'))
            ->join('services','reservations.service_id','services.id')
            ->groupBy('services.category_id')
            ->with('category')
            ->get();
    }

    public function usageMetrics()
    {
        return [
          'monthly_reservations' => Reservation::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
                                          ->groupBy('month')->get(),
          'active_users'        => DB::table('users')->where('last_login_at','>=',now()->subMonth())->count(),
        ];
    }
}
