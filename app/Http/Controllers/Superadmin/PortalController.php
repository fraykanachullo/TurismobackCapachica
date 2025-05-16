<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:portals',
            'language' => 'required|string|max:10',
            'theme_colors' => 'nullable|array',
        ]);

        $portal = Portal::create([
            'name' => $validated['name'],
            'subdomain' => $validated['subdomain'],
            'language' => $validated['language'],
            'theme_colors' => $validated['theme_colors'] ?? null,
        ]);

        return response()->json(['mensaje' => 'Portal creado', 'portal' => $portal]);
    }

    public function index()
    {
        return response()->json(Portal::all());
    }
}
