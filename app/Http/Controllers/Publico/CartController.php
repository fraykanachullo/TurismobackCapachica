<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Promotion;

class CartController extends Controller
{
    // GET /api/cart
    public function index(Request $request)
    {
        return response()->json($request->session()->get('cart', []));
    }

    // GET /api/cart/summary
    public function summary(Request $request)
    {
        $cart  = collect($request->session()->get('cart', []));
        $total = $cart->sum(fn($i) => $i['price'] * $i['quantity']);
        return response()->json(['cart' => $cart, 'total' => $total]);
    }

    // POST /api/cart/service/{serviceId}
    public function addService(Request $request, $serviceId)
    {
        $svc  = Service::findOrFail($serviceId);
        $cart = collect($request->session()->get('cart', []));

        if (! $cart->contains(fn($i) => $i['type']=='service' && $i['id']==$svc->id)) {
            $cart->push([
                'type'     => 'service',
                'id'       => $svc->id,
                'title'    => $svc->title,
                'price'    => (float)$svc->price,
                'quantity' => 1,
            ]);
        }

        $request->session()->put('cart', $cart->toArray());
        return response()->json(['cart' => $cart]);
    }

    // POST /api/cart/promotion/{promoId}
    public function addPromotion(Request $request, $promoId)
    {
        $promo = Promotion::active()->with('services')->findOrFail($promoId);
        $price = $promo->services->sum(fn($s) =>
            round($s->price * (1 - $promo->discount_percentage/100), 2)
        );
        $cart  = collect($request->session()->get('cart', []));

        if (! $cart->contains(fn($i) => $i['type']=='promotion' && $i['id']==$promo->id)) {
            $cart->push([
                'type'     => 'promotion',
                'id'       => $promo->id,
                'title'    => $promo->title,
                'price'    => $price,
                'quantity' => 1,
            ]);
        }

        $request->session()->put('cart', $cart->toArray());
        return response()->json(['cart' => $cart]);
    }

    // DELETE /api/cart/{type}/{id}
    public function remove(Request $request, $type, $id)
    {
        $cart = collect($request->session()->get('cart', []))
            ->reject(fn($i) => $i['type']==$type && $i['id']==(int)$id)
            ->values();

        $request->session()->put('cart', $cart->toArray());
        return response()->json(['cart' => $cart]);
    }

    public function clear(Request $request)
{
    $request->session()->forget('cart');
    return response()->json([
        'message' => 'Carrito vaciado.',
        'cart'    => []
    ]);
}
}
