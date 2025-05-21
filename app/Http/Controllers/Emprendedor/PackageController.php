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

            public function show($id)
        {
            $package = Package::where('company_id', Auth::user()->company->id)->findOrFail($id);
            return response()->json($package);
        }

        public function update(Request $request, $id)
        {
            $package = Package::where('company_id', Auth::user()->company->id)->findOrFail($id);

            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:pending,active,paused,rejected',
            ]);

            $package->update($data);

            return response()->json(['message' => 'Paquete actualizado', 'package' => $package]);
        }

        public function destroy($id)
        {
            $package = Package::where('company_id', Auth::user()->company->id)->findOrFail($id);
            $package->delete();

            return response()->noContent();
        }

}
