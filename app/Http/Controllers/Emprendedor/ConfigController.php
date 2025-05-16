<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function show()
    {
        $companyId = Auth::user()->company->id;
        return Config::where('company_id', $companyId)->pluck('value','key');
    }

    public function update(Request $request)
    {
        $companyId = Auth::user()->company->id;
        foreach ($request->all() as $key => $value) {
            Config::updateOrCreate(
                ['company_id' => $companyId, 'key' => $key],
                ['value' => $value]
            );
        }
        return response()->json(['message' => 'Configuración actualizada']);
    }
}
