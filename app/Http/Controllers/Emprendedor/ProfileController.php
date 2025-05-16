<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return Auth::user()->company()->with('location')->first();
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;

        $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'service_type' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'location_id' => 'sometimes|exists:locations,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo_url = asset('storage/' . $path);
        }

        $company->fill($request->except('logo'));
        $company->save();

        return response()->json(['message' => 'Perfil actualizado', 'company' => $company]);
    }
}
