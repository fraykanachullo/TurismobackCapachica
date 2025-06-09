<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Company;

class SuperadminController extends Controller
{
    






    //  nuevas funciones 
    /**
     * 📋 Listar empresas con estado 'pendiente'
     */
    public function listarEmpresasPendientes()
    {
        $empresas = Company::where('status', 'pendiente')
            ->with('user:id,name,email') // incluir datos del usuario que la creó
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'empresas' => $empresas
        ]);
    }

     /**
     * ✅ Aprobar una empresa
     */
    public function aprobarEmpresa($id)
    {
        $empresa = Company::findOrFail($id);

        if ($empresa->status === 'aprobada') {
            return response()->json([
                'mensaje' => 'La empresa ya ha sido aprobada.',
                'empresa' => $empresa
            ], 409);
        }

        $empresa->status = 'aprobada';
        $empresa->verified_at = now();
        $empresa->save();

        return response()->json([
            'mensaje' => 'Empresa aprobada exitosamente.',
            'empresa' => $empresa
        ]);
    }
      /**
     * ❌ Rechazar una empresa
     */
    public function rechazarEmpresa($id)
    {
        $empresa = Company::findOrFail($id);

        if ($empresa->status === 'rechazada') {
            return response()->json([
                'mensaje' => 'La empresa ya ha sido rechazada.',
                'empresa' => $empresa
            ], 409);
        }

        $empresa->status = 'rechazada';
        $empresa->save();

        return response()->json([
            'mensaje' => 'Empresa rechazada exitosamente.',
            'empresa' => $empresa
        ]);
    }

    public function listarTodasLasEmpresas()
    {
        $empresas = Company::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'empresas' => $empresas
        ]);
    }

}
