<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod; // Asegúrate de tener este modelo
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|max:19',
            'cardholder_name' => 'required|string|max:255',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'cvv' => 'required|string|max:4',
            'brand' => 'nullable|string|max:50', // Visa, MasterCard, etc.
        ]);

        $paymentMethod = PaymentMethod::create([
            'user_id' => Auth::id(),
            'card_number' => $request->card_number,
            'cardholder_name' => $request->cardholder_name,
            'expiry_month' => $request->expiry_month,
            'expiry_year' => $request->expiry_year,
            'cvv' => $request->cvv,
            'brand' => $request->brand,
        ]);

        return response()->json(['message' => 'Método de pago agregado', 'payment_method' => $paymentMethod], 201);
    }

    public function show($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        return $paymentMethod;
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'card_number' => 'sometimes|string|max:19',
            'cardholder_name' => 'sometimes|string|max:255',
            'expiry_month' => 'sometimes|integer|min:1|max:12',
            'expiry_year' => 'sometimes|integer|min:' . date('Y'),
            'cvv' => 'sometimes|string|max:4',
            'brand' => 'nullable|string|max:50',
        ]);

        $paymentMethod->update($request->all());

        return response()->json(['message' => 'Método de pago actualizado', 'payment_method' => $paymentMethod]);
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        $paymentMethod->delete();
        return response()->noContent();
    }
}
