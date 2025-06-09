<?php
// app/Http/Controllers/Superadmin/CommunityManagementController.php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;

class CommunityManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:superadmin']);
    }

    // GET /superadmin/comunidades
    public function index()
    {
        $comunidades = Location::where('type','comunidad')
            ->get()
            ->map(fn($loc)=> $loc->only([
                'id','name','descripcion_corta','descripcion_larga',
                'atractivos','habitantes','estado','imagen','galeria'
            ]));

        return response()->json($comunidades);
    }

    // POST /superadmin/comunidades
    public function store(StoreLocationRequest $r)
    {
        $data = $r->validated();

        if ($file = $r->file('imagen')) {
            $path = $file->store('comunidades','public');
            $data['imagen'] = Storage::url($path);
        }

        if ($r->hasFile('galeria')) {
            $urls=[];
            foreach ($r->file('galeria') as $img) {
                $p = $img->store('comunidades/galeria','public');
                $urls[]= Storage::url($p);
            }
            $data['galeria'] = $urls;
        }

        $loc = Location::create($data);

        return response()->json([
            'message'   => 'Comunidad creada con éxito.',
            'community' => $loc->only([
                'id','name','descripcion_corta','descripcion_larga',
                'atractivos','habitantes','estado','imagen','galeria'
            ])
        ], 201);
    }

    // GET /superadmin/comunidades/{community}
    public function show(Location $community)
    {
        if ($community->type!=='comunidad') {
            return response()->json(['message'=>'No es comunidad'],404);
        }
        return response()->json(
            $community->only([
                'id','name','descripcion_corta','descripcion_larga',
                'atractivos','habitantes','estado','imagen','galeria'
            ])
        );
    }

    // PUT /superadmin/comunidades/{community}
    public function update(UpdateLocationRequest $r, Location $community)
    {
        if ($community->type!=='comunidad') {
            return response()->json(['message'=>'No es comunidad'],404);
        }

        $data = $r->validated();

        if ($file = $r->file('imagen')) {
            $path = $file->store('comunidades','public');
            $data['imagen'] = Storage::url($path);
        }
        if ($r->hasFile('galeria')) {
            $urls=[];
            foreach ($r->file('galeria') as $img) {
                $p = $img->store('comunidades/galeria','public');
                $urls[]= Storage::url($p);
            }
            $data['galeria'] = $urls;
        }

        $community->update($data);

        return response()->json([
            'message'   => 'Comunidad actualizada correctamente.',
            'community' => $community->only([
                'id','name','descripcion_corta','descripcion_larga',
                'atractivos','habitantes','estado','imagen','galeria'
            ])
        ]);
    }

    // DELETE /superadmin/comunidades/{community}
    public function destroy(Location $community)
    {
        if ($community->type!=='comunidad') {
            return response()->json(['message'=>'No es comunidad'],404);
        }
        $community->update(['estado'=>'inactiva']);
        return response()->json(['message'=>'Comunidad inactivada correctamente.']);
    }
}
