<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Config; // usa tabla configs para almacenar configuraciones generales
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function show()
    {
        // Retornar configuración personalizada del usuario o valores por defecto
        // Por simplicidad devolvemos todo con clave-valor
        return Config::where('user_id', Auth::id())->pluck('value', 'key');
    }

    public function update(Request $request)
    {
        $allowedKeys = ['language', 'notifications', 'dark_mode'];

        foreach ($request->only($allowedKeys) as $key => $value) {
            Config::updateOrCreate(
                ['user_id' => Auth::id(), 'key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Configuración actualizada']);
    }
}
