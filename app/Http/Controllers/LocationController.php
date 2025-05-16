<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * GET /api/locations
     */
    public function index(): JsonResponse
    {
        return response()->json(Location::all());
    }
}
