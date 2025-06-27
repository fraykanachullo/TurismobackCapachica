<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:emprendedor']);
    }

    /**
     * List all promotions of the authenticated entrepreneur.
     */
    public function index()
    {
        $company = Auth::user()->company;

        $promotions = $company
            ->promotions()
            ->with('services')
            ->get()
            ->map(fn($promo) => $this->formatPromotion($promo));

        return response()->json($promotions, Response::HTTP_OK);
    }

    /**
     * Create a new promotion.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'status'              => 'required|in:pending,active,expired,inactive',
            'service_ids'         => 'nullable|array',
            'service_ids.*'       => 'exists:services,id',
        ]);

        $company   = Auth::user()->company;
        $promotion = $company->promotions()->create($data);

        if (!empty($data['service_ids'])) {
            $promotion->services()->sync($data['service_ids']);
        }

        return response()->json(
            $this->formatPromotion($promotion->load('services')),
            Response::HTTP_CREATED
        );
    }

    /**
     * Show a single promotion.
     */
    public function show($id)
    {
        $company   = Auth::user()->company;
        $promotion = $company
            ->promotions()
            ->with('services')
            ->findOrFail($id);

        return response()->json(
            $this->formatPromotion($promotion),
            Response::HTTP_OK
        );
    }

    /**
     * Update an existing promotion.
     */
    public function update(Request $request, $id)
    {
        $company   = Auth::user()->company;
        $promotion = $company->promotions()->findOrFail($id);

        $data = $request->validate([
            'title'               => 'sometimes|string|max:255',
            'description'         => 'sometimes|string',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
            'start_date'          => 'sometimes|date',
            'end_date'            => 'sometimes|date|after_or_equal:start_date',
            'status'              => 'sometimes|in:pending,active,expired,inactive',
            'service_ids'         => 'nullable|array',
            'service_ids.*'       => 'exists:services,id',
        ]);

        $promotion->update($data);

        if (array_key_exists('service_ids', $data)) {
            $promotion->services()->sync($data['service_ids'] ?? []);
        }

        return response()->json(
            $this->formatPromotion($promotion->load('services')),
            Response::HTTP_OK
        );
    }

    /**
     * Delete a promotion.
     */
    public function destroy($id)
    {
        $company   = Auth::user()->company;
        $promotion = $company->promotions()->findOrFail($id);
        $promotion->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Formatea una promoción para la respuesta JSON.
     */
    protected function formatPromotion(Promotion $promo): array
    {
        $services = $promo->services->map(function ($service) use ($promo) {
            $before = (float)$service->price;
            $after  = round($before * (1 - $promo->discount_percentage / 100), 2);

            return [
                'id'           => $service->id,
                'title'        => $service->title,
                'price_before' => $before,
                'price_after'  => $after,
            ];
        });

        return [
            'id'                  => $promo->id,
            'title'               => $promo->title,
            'description'         => $promo->description,
            'discount_percentage' => $promo->discount_percentage,
            'start_date'          => $promo->start_date->toDateString(),
            'end_date'            => $promo->end_date->toDateString(),
            'status'              => $promo->status,
            'services'            => $services,
            'total_price_before'  => $services->sum('price_before'),
            'total_price_after'   => $services->sum('price_after'),
        ];
    }
}
