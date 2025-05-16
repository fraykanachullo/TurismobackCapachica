<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        return Package::where('company_id', Auth::user()->company->id)->with('services')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $package = Auth::user()->company->packages()->create([
            'title' => $request->title,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        $package->services()->sync($request->service_ids);

        return response()->json(['message' => 'Paquete creado', 'package' => $package->load('services')], 201);
    }

    // show, update, destroy similares...
}
