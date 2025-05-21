<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        return Auth::user()->favorites()->with('service')->get();
    }

    public function store(Request $request)
    {
        $request->validate(['service_id' => 'required|exists:services,id']);
        Auth::user()->favorites()->syncWithoutDetaching([$request->service_id]);
        return response()->json(['message' => 'Agregado a favoritos']);
    }

    public function destroy($id)
    {
        Auth::user()->favorites()->detach($id);
        return response()->noContent();
    }
}
