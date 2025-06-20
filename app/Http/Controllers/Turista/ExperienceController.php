<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('status','active')
            ->with([
                'category',
                'zone',
                'media',
                'promotions'  => fn($q)=> $q->active(),
                'itineraries'
            ]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        return $query->paginate(10);
    }
}
