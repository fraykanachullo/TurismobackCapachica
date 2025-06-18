<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEmprendedorRequest;

class EmprendedoresController extends Controller
{
    public function __construct()
    {
        // Protege todas las rutas: Sanctum + rol superadmin
        $this->middleware(['auth:sanctum', 'role:superadmin']);
    }

    /**
     * 1. KPIs generales y por estado
     * GET /superadmin/emprendedores/kpis
     */
    public function kpis()
    {
        $base = User::role('emprendedor');

        return response()->json([
            'total'      => $base->count(),
            'activos'    => (clone $base)->where('estado','activo')->count(),
            'pendientes' => (clone $base)->where('estado','pendiente')->count(),
            'suspendidos'=> (clone $base)->where('estado','suspendido')->count(),
        ]);
    }

    /**
     * 2. Listar emprendedores con filtros y paginación
     * GET /superadmin/emprendedores
     */
    public function index(Request $request)
    {
        $q = User::role('emprendedor');

        if ($s = $request->query('search')) {
            $q->where(fn($sub) =>
                $sub->where('name','like',"%{$s}%")
                    ->orWhere('email','like',"%{$s}%")
            );
        }
        if ($e = $request->query('estado')) {
            $q->where('estado',$e);
        }

        $pag = $q->orderBy('created_at','desc')
                 ->paginate($request->query('per_page',10));

        $data = $pag->getCollection()->transform(fn(User $u)=>[
            'id'             => $u->id,
            'nombre'         => $u->name,
            'email'          => $u->email,
            'estado'         => $u->estado,
            'fecha_registro' => $u->created_at->toDateString(),
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'total'        => $pag->total(),
                'per_page'     => $pag->perPage(),
                'current_page' => $pag->currentPage(),
            ],
        ]);
    }

    /**
     * 3. Crear nuevo emprendedor con opción de subir foto
     * POST /superadmin/emprendedores
     */
    public function store(StoreEmprendedorRequest $request)
    {
        $data = $request->validated();

        // Manejo de upload de imagen
        if ($file = $request->file('imagen')) {
            $path = $file->store('emprendedores','public');
            $data['foto'] = Storage::url($path);
        }

        // Forzar estado activo al crear
        $data['estado'] = 'activo';
        $data['password'] = Hash::make($data['password']);

        $user = User::create([
            'name'     => $data['nombre'], 
            'email'    => $data['email'],
            'password' => $data['password'],
            'estado'   => $data['estado'],
            'foto'     => $data['foto'] ?? null,
        ]);
        $user->assignRole('emprendedor');

        return response()->json([
            'message'     => 'Emprendedor creado con éxito.',
            'emprendedor' => [
                'id'     => $user->id,
                'nombre' => $user->name,
                'email'  => $user->email,
                'estado' => $user->estado,
                'foto'   => $user->foto,
                'fecha_registro'  => $user->created_at->toDateString(),  // ← aquí
            ],
        ], 201);
    }

    /**
     * 4. Ver detalle de un emprendedor
     * GET /superadmin/emprendedores/{id}
     */
    public function show($id)
    {
        $user = User::role('emprendedor')
                    ->with('company')
                    ->findOrFail($id);

        return response()->json([
            'id'              => $user->id,
            'nombre'          => $user->name,
            'email'           => $user->email,
            'estado'          => $user->estado,
            'fecha_registro'  => $user->created_at->toDateString(),
            'foto'            => $user->foto,
            'empresa'         => [
                'nombre'    => $user->company->business_name ?? null,
                'comunidad' => $user->company->community ?? null,
                'celular'   => $user->company->phone ?? null,
            ],
        ]);
    }

    /**
     * 5. Cambiar estado de un emprendedor
     * PUT /superadmin/emprendedores/{id}/estado
     */
    public function updateEstado(Request $request, $id)
    {
        $request->validate(['estado'=>'required|in:activo,pendiente,suspendido']);
        $user = User::role('emprendedor')->findOrFail($id);
        $user->update(['estado'=>$request->input('estado')]);

        return response()->json([
            'message'=>'Estado actualizado correctamente.',
            'estado' =>$user->estado,
        ]);
    }

    /**
     * 6. Suspender emprendedor
     * DELETE /superadmin/emprendedores/{id}
     */
    public function destroy($id)
    {
        $user = User::role('emprendedor')->findOrFail($id);
        $user->update(['estado'=>'suspendido']);

        return response()->json(['message'=>'Emprendedor suspendido correctamente.']);
    }
}
