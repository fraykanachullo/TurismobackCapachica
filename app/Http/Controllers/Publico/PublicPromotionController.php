<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Promotion;

class PublicPromotionController extends Controller
{
    // GET /api/promociones-publicas
    public function index()
    {
        $promos = Promotion::active()
            ->with(['services'=>fn($q)=>$q->with(['media','zone','category','itineraries'])])
            ->get()
            ->map(fn($promo) => [
                'id'                   => $promo->id,
                'title'                => $promo->title,
                'description'          => $promo->description,
                'discount_percentage'  => $promo->discount_percentage,
                'start_date'           => $promo->start_date->toDateString(),
                'end_date'             => $promo->end_date->toDateString(),
                'services'             => $promo->services->map(fn($srv) => [
                    'id'           => $srv->id,
                    'title'        => $srv->title,
                    'price_before' => (float)$srv->price,
                    'price_after'  => round($srv->price*(1-$promo->discount_percentage/100),2),
                    'itineraries'  => $srv->itineraries->map(fn($it) => [
                        'day'         => $it->day_number,
                        'start_time'  => $it->start_time,
                        'end_time'    => $it->end_time,
                        'title'       => $it->title,
                        'description' => $it->description,
                    ]),
                ]),
                'total_price_before'   => $promo->services->sum(fn($s)=>(float)$s->price),
                'total_price_after'    => $promo->services->sum(fn($s)=>round($s->price*(1-$promo->discount_percentage/100),2)),
            ]);

        return response()->json($promos);
    }
}
