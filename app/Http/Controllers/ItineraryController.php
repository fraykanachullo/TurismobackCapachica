<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItineraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $all = Itinerary::all();
        return response()->json($all, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'itineraryable_type' => 'required|string',
            'itineraryable_id'   => 'required|integer',
            'day_number'         => 'nullable|integer|min:1',
            'start_time'         => 'nullable|date_format:H:i',
            'end_time'           => 'nullable|date_format:H:i|after_or_equal:start_time',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
        ]);

        $itinerary = Itinerary::create($data);
        return response()->json($itinerary, Response::HTTP_CREATED);
    }

    public function show(Itinerary $itinerary)
    {
        return response()->json($itinerary, Response::HTTP_OK);
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $data = $request->validate([
            'day_number'  => 'sometimes|integer|min:1',
            'start_time'  => 'sometimes|date_format:H:i',
            'end_time'    => 'sometimes|date_format:H:i|after_or_equal:start_time',
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $itinerary->update($data);
        return response()->json($itinerary, Response::HTTP_OK);
    }

    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
