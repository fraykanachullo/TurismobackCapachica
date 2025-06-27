<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class EmprendedorController extends Controller
{
    public function crearEmpresa(Request $request)
    {
      

        $request->validate([
            'ruc'           => 'required|digits:11|unique:companies,ruc',
            'business_name' => 'required|string|max:255',
            'trade_name'    => 'nullable|string|max:255',
            'service_type'  => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'phone'         => 'nullable|string|max:20',
            'website'       => 'nullable|url|max:255',
            'description'   => 'nullable|string',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            'location_id'   => 'required|exists:locations,id', // Validación para location_id
        ]);

        // Subir logo si existe
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $empresa = Company::create([
            'user_id'       => $request->user()->id,
            'ruc'           => $request->ruc,
            'business_name' => $request->business_name,
            'trade_name'    => $request->trade_name,
            'service_type'  => $request->service_type,
            'contact_email' => $request->contact_email,
            'phone'         => $request->phone,
            'website'       => $request->website,
            'description'   => $request->description,
            'logo_url'      => $logoPath ? asset('storage/' . $logoPath) : null,
            'status'        => 'pendiente',
            'location_id'   => $request->location_id,  // Asegúrate de que location_id se pase
        ]);

        return response()->json([
            'mensaje' => 'Empresa registrada correctamente. Pendiente de aprobación.',
            'empresa' => $empresa
        ], 201);
    }

    public function estadoEmpresa(Request $request)
    {
        $empresa = Company::where('user_id', $request->user()->id)->first();

        if (!$empresa) {
            return response()->json(['estado' => 'no registrada']);
        }

        return response()->json(['estado' => $empresa->status]);
    }
}
