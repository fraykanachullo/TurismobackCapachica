<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Notifications\AdminMessageNotification;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:superadmin']);
    }

    /**
     * Listar turistas con filtros, paginación y transformación a claves en español.
     */
    public function index(Request $request)
    {
        $query = User::role('turista')->withCount('reservations');

        // --- BÚSQUEDA ---
        if ($q = $request->input('search')) {
            $query->where(function($q2) use ($q) {
                $q2->where('name','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%");
            });
        }

        // --- FILTROS ---
        if (!is_null($e = $request->input('estado'))) {
            $query->where('estado', $e);
        }
        if ($from = $request->input('date_from')) {
            $query->whereDate('created_at','>=',$from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('created_at','<=',$to);
        }
        if ($min = $request->input('min_reservas')) {
            $query->has('reservations','>=',(int)$min);
        }

        // --- ORDENAMIENTO ---
        $allowed = ['name','created_at','reservations_count'];
        $sortBy  = in_array($request->input('sort_by'), $allowed)
                   ? $request->input('sort_by')
                   : 'name';
        $sortDir = strtolower($request->input('sort_order')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        // --- PAGINACIÓN ---
        $perPage = (int)$request->input('per_page', 15);
        $pag     = $query->paginate($perPage);

        // --- TRANSFORMACIÓN A LLAVES EN ESPAÑOL ---
        $pag->getCollection()->transform(function (User $u) {
            return [
                'id'       => $u->id,
                'nombre'   => $u->name,
                'correo'   => $u->email,
                'foto'     => $u->foto,
                'fecha'    => $u->created_at->toDateString(),
                'estado'   => $u->estado,
                'reservas' => $u->reservations_count,
                'rating'   => round($u->averageRating, 2),
            ];
        });

        return response()->json($pag);
    }

    /**
     * Detalle de un turista
     */
    public function show($id)
    {
        $u = User::role('turista')
                 ->withCount('reservations')
                 ->with(['reservations','reviews'])
                 ->findOrFail($id);

        return response()->json([
            'id'           => $u->id,
            'nombre'       => $u->name,
            'correo'       => $u->email,
            'foto'         => $u->foto,
            'fecha'        => $u->created_at->toDateTimeString(),
            'estado'       => $u->estado,
            'reservas'     => $u->reservations_count,
            'rating'       => round($u->averageRating, 2),
            'detalleReservas' => $u->reservations,
            'detalleResenas'  => $u->reviews,
        ]);
    }

    /**
     * Cambiar estado (activo / bloqueado)
     */
    public function updateStatus(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'estado' => 'required|in:activo,bloqueado',
        ]);
        if ($v->fails()) {
            return response()->json(['errors'=>$v->errors()], 422);
        }

        $u = User::findOrFail($id);
        $u->estado = $request->input('estado');
        $u->save();

        return response()->json([
            'message'=>'Estado actualizado correctamente.',
            'estado' =>$u->estado,
        ]);
    }

    /**
     * Enviar mensaje directo a un turista
     */
    public function sendMessage(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'mensaje' => 'required|string|max:1000',
        ]);
        if ($v->fails()) {
            return response()->json(['errors'=>$v->errors()], 422);
        }

        $u = User::findOrFail($id);
        $u->notify(new AdminMessageNotification($request->input('mensaje')));

        return response()->json(['message'=>'Mensaje enviado correctamente.']);
    }
}
