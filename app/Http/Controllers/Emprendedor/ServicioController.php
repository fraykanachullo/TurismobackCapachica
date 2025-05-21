<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    public function index()
    {
        $services = Service::whereHas('company', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['media','category','zone'])
            ->latest()
            ->get();

        return response()->json($services);
    }

    public function store(Request $request)
    {
        $request->validate([
          'title'        => 'required|string|max:255',
          'type'         => 'required|in:tour,hospedaje,gastronomia,experiencia',
          'description'  => 'required|string',
          'location'     => 'required|string',
          'price'        => 'required|numeric|min:0',
          'capacity'     => 'nullable|integer|min:1',
          'duration'     => 'nullable|string|max:100',
          'policy_cancellation' => 'nullable|string',
          'category_id'  => 'required|exists:categories,id',
          'location_id'  => 'required|exists:locations,id',
        ]);

        $company = Auth::user()->company;
        if (!$company || $company->status !== 'aprobada') {
            return response()->json(['message'=>'Empresa no aprobada'],403);
        }

        $service = $company->services()->create([
          'title'               => $request->title,
          'slug'                => Str::slug($request->title).'-'.uniqid(),
          'type'                => $request->type,
          'description'         => $request->description,
          'location'            => $request->location,
          'price'               => $request->price,
          'capacity'            => $request->capacity,
          'duration'            => $request->duration,
          'policy_cancellation' => $request->policy_cancellation,
          'status'              => 'pending',
          'category_id'         => $request->category_id,
          'location_id'         => $request->location_id,
        ]);

        return response()->json([
          'message'=>'Servicio creado exitosamente.',
          'service'=> $service->load('category','zone')
        ], 201);
    }

    public function show($id)
    {
        $service = Service::with(['media','category','zone'])->findOrFail($id);
        if ($service->company->user_id !== Auth::id()) {
            return response()->json(['message'=>'No autorizado'],403);
        }
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        if ($service->company->user_id !== Auth::id()) {
            return response()->json(['message'=>'No autorizado'],403);
        }
        $data = $request->validate([
          'title','type','description','location','price',
          'capacity','duration','policy_cancellation',
          'category_id','location_id'
        ]);
        $service->update($data);

        return response()->json([
          'message'=>'Servicio actualizado.',
          'service'=> $service->fresh()->load('category','zone')
        ]);
        }

        public function destroy($id)
        {
        $service = Service::findOrFail($id);
        if ($service->company->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $service->delete();
        return response()->noContent();
        }


        public function toggleActive(Request $request, $id)
        {
            $service = Service::findOrFail($id);
            if ($service->company->user_id !== Auth::id()) {
                return response()->json(['message'=>'No autorizado'],403);
            }
            // toggle entre pending ↔ active
            $service->status = $service->status === 'active' ? 'pending' : 'active';
            $service->save();
            return response()->json(['status'=>$service->status]);
        }



}

