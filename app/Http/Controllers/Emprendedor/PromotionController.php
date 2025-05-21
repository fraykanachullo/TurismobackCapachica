<?php
namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
     public function index()
    {
        $companyId = Auth::user()->company->id;
        return Promotion::where('company_id', $companyId)->with('services')->get();
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_ids' => 'nullable|array',
        'service_ids.*' => 'exists:services,id',
    ]);

    $promotion = Auth::user()->company->promotions()->create($request->only([
        'title', 'description', 'discount_percentage', 'start_date', 'end_date', 'status'
    ]));

    if ($request->has('service_ids')) {
        $promotion->services()->sync($request->service_ids);
    }

    return response()->json(['message' => 'Promoción creada', 'promotion' => $promotion->load('services')], 201);
}

    public function show($id)
    {
        $promotion = Promotion::with('services')->findOrFail($id);
        $this->authorizeCompany($promotion->company_id);
        return $promotion;
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        $this->authorizeCompany($promotion->company_id);
    
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);
    
        $promotion->update($request->only([
            'title', 'description', 'discount_percentage', 'start_date', 'end_date', 'status'
        ]));
    
        if ($request->has('service_ids')) {
            $promotion->services()->sync($request->service_ids);
        }
    
        return response()->json(['message' => 'Promoción actualizada', 'promotion' => $promotion->load('services')]);
    }

    private function authorizeCompany($companyId)
    {
        if ($companyId !== Auth::user()->company->id) {
            abort(403, 'No autorizado');
        }
    }

        public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $this->authorizeCompany($promotion->company_id);

        $promotion->delete();
        return response()->noContent();
    }
}
