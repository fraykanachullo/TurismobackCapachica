<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalDesign;

class PortalDesignController extends Controller
{
    public function show($id)
    {
        $design = PortalDesign::where('portal_id', $id)->first();
        return response()->json($design);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'portal_id' => 'required|exists:portals,id',
            'slider_images' => 'nullable|array',
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'sections' => 'nullable|array',
            'translations' => 'nullable|array',
            'status' => 'in:borrador,publicado'
        ]);

        $design = PortalDesign::updateOrCreate(
            ['portal_id' => $validated['portal_id']],
            $validated
        );

        return response()->json(['mensaje' => 'Diseño guardado', 'diseño' => $design]);
    }

    public function update(Request $request, $id)
    {
        $design = PortalDesign::findOrFail($id);

        $validated = $request->validate([
            'slider_images' => 'nullable|array',
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'sections' => 'nullable|array',
            'translations' => 'nullable|array',
            'status' => 'in:borrador,publicado'
        ]);

        $design->update($validated);

        return response()->json(['mensaje' => 'Diseño actualizado', 'diseño' => $design]);
    }

    public function destroy($id)
    {
        $design = PortalDesign::findOrFail($id);
        $design->delete();

        return response()->json(['mensaje' => 'Diseño eliminado']);
    }
}
