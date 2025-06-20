<?php
namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $paginator = Promotion::active()
            ->with(['services'=> fn($q)=> $q->with(['media','category','zone','itineraries'])])
            ->paginate(10);

        // Opcional: mapear precios antes/después en la página actual
        $paginator->getCollection()->transform(function($promo) {
            $services = $promo->services->map(fn($srv) => [
                'id'            => $srv->id,
                'title'         => $srv->title,
                'price_before'  => (float)$srv->price,
                'price_after'   => round($srv->price * (1 - $promo->discount_percentage/100), 2),
                'itineraries'   => $srv->itineraries->map(fn($it) => [
                    'day'         => $it->day_number,
                    'start_time'  => $it->start_time,
                    'end_time'    => $it->end_time,
                    'title'       => $it->title,
                    'description' => $it->description,
                ]),
            ]);

            return [
                'id'                   => $promo->id,
                'title'                => $promo->title,
                'description'          => $promo->description,
                'discount_percentage'  => $promo->discount_percentage,
                'start_date'           => $promo->start_date->toDateString(),
                'end_date'             => $promo->end_date->toDateString(),
                'services'             => $services,
                'total_price_before'   => $services->sum('price_before'),
                'total_price_after'    => $services->sum('price_after'),
            ];
        });

        return response()->json($paginator);
    }
}
