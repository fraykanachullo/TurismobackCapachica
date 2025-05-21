<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()->with(['category', 'zone', 'media'])
                    ->where('status', 'active');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->has('rating')) {
            // Puedes implementar filtro por rating usando reviews, si tienes promedio calculado
        }

        return $query->paginate(10);
    }
}
