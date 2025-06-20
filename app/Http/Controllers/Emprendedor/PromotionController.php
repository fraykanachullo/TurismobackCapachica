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
        $this->middleware('auth:sanctum');
        $this->middleware('role:emprendedor');
    }

    /**
     * List all promotions of the authenticated entrepreneur,
     * including per-service price before/after discount and totals.
     */
    public function index()
    {
        $companyId = Auth::user()->company->id;
        $promotions = Promotion::where('company_id', $companyId)
            ->with('services')
            ->get()
            ->map(function ($promo) {
                $services = $promo->services->map(function ($service) use ($promo) {
                    $priceBefore = (float)$service->price;
                    $priceAfter  = round($priceBefore * (1 - $promo->discount_percentage / 100), 2);

                    return [
                        'id'           => $service->id,
                        'title'        => $service->title,
                        'price_before' => $priceBefore,
                        'price_after'  => $priceAfter,
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
            });

        return response()->json($promotions, Response::HTTP_OK);
    }

    /**
     * Create a new promotion and return its full detail.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'status'              => 'required|in:pending,active,expired',
            'service_ids'         => 'nullable|array',
            'service_ids.*'       => 'exists:services,id',
        ]);

        $company   = Auth::user()->company;
        $promotion = $company->promotions()->create($data);

        if (!empty($data['service_ids'])) {
            $promotion->services()->sync($data['service_ids']);
        }

        // Return the newly created promotion with calculated prices
        return $this->show($promotion);
    }

    /**
     * Show a single promotion with price breakdown.
     */
    public function show(Promotion $promotion)
    {
        $this->authorizeCompany($promotion->company_id);
        $promo = $promotion->load('services');

        $services = $promo->services->map(function ($service) use ($promo) {
            $priceBefore = (float)$service->price;
            $priceAfter  = round($priceBefore * (1 - $promo->discount_percentage / 100), 2);

            return [
                'id'           => $service->id,
                'title'        => $service->title,
                'price_before' => $priceBefore,
                'price_after'  => $priceAfter,
            ];
        });

        return response()->json([
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
        ], Response::HTTP_OK);
    }

    /**
     * Update an existing promotion and return updated detail.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $this->authorizeCompany($promotion->company_id);

        $data = $request->validate([
            'title'               => 'sometimes|string|max:255',
            'description'         => 'sometimes|string',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
            'start_date'          => 'sometimes|date',
            'end_date'            => 'sometimes|date|after_or_equal:start_date',
            'status'              => 'sometimes|in:pending,active,expired',
            'service_ids'         => 'nullable|array',
            'service_ids.*'       => 'exists:services,id',
        ]);

        $promotion->update($data);

        if (isset($data['service_ids'])) {
            $promotion->services()->sync($data['service_ids']);
        }

        // Return the updated promotion with recalculated prices
        return $this->show($promotion);
    }

    /**
     * Delete a promotion.
     */
    public function destroy(Promotion $promotion)
    {
        $this->authorizeCompany($promotion->company_id);
        $promotion->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Ensure the authenticated user belongs to the same company.
     */
    protected function authorizeCompany($companyId)
    {
        if ($companyId !== Auth::user()->company->id) {
            abort(Response::HTTP_FORBIDDEN, 'No autorizado');
        }
    }
}
