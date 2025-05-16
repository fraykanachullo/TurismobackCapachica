<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config;

class ConfigController extends Controller
{
    // Mostrar toda la configuración
    public function index()
    {
        return Config::all();
    }

    // Actualizar configuraciones (por ejemplo con un JSON de key-value)
    public function update(Request $request)
    {
        $data = $request->all(); // recibe array tipo ['key' => 'value', ...]

        foreach ($data as $key => $value) {
            Config::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'message' => 'Configuración actualizada correctamente.',
            'configs' => Config::all()
        ]);
    }
}
