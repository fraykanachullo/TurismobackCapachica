<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class CartController extends Controller
{
    // Ver contenido del carrito
    public function index(Request $request)
    {
        return response()->json($request->session()->get('cart', []));
    }

    // Suma total del carrito
    public function summary(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        $total = $cart->sum(fn($item) => $item['price'] * $item['quantity']);
        return response()->json([
            'cart'  => $cart,
            'total' => $total,
        ]);
    }

    // Añadir servicio al carrito
    public function add(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $cart = collect($request->session()->get('cart', []));
        if (! $cart->contains('id', $service->id)) {
            $cart->push([
                'id'       => $service->id,
                'title'    => $service->title,
                'price'    => $service->price,
                'quantity' => 1,
            ]);
        }
        $request->session()->put('cart', $cart->toArray());
        return response()->json(['cart' => $cart]);
    }

    // Quitar servicio del carrito
    public function remove(Request $request, $serviceId)
    {
        $cart = collect($request->session()->get('cart', []))
                 ->reject(fn($item) => $item['id'] == $serviceId)
                 ->values();
        $request->session()->put('cart', $cart->toArray());
        return response()->json(['cart' => $cart]);
    }
}
