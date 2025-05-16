<?php
namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExperienceController extends Controller
{
    public function index()
    {
        return Service::whereHas('company', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['category','zone','media'])
            ->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'duration' => 'nullable|string',
            'policy_cancellation' => 'nullable|string',
            'type' => 'required|in:tour,hospedaje,gastronomia,experiencia',
        ]);

        $company = Auth::user()->company;
        if (!$company || $company->status !== 'aprobada') {
            return response()->json(['message' => 'Empresa no válida o no aprobada'], 403);
        }

        $service = $company->services()->create(array_merge(
            $request->only(['title','category_id','location_id','description','price','capacity','duration','policy_cancellation','type']),
            ['slug' => Str::slug($request->title).'-'.uniqid(),'status'=>'pending']
        ));

        return response()->json(['message' => 'Experiencia creada','service'=>$service], 201);
    }

    // show, update, destroy igual al servicio anterior...
}
