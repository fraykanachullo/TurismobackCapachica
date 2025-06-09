<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\Location;

class ExperienceManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:superadmin']);
    }

    /**
     * 🔍 Listar “experiencias publicadas” 
     *     —> filtramos por status = 'active'
     * GET /superadmin/experiencias/publicadas
     */
    public function published(Request $r)
    {
        // En tu servicio creas con 'status' => 'active'
        $q = Service::where('status', 'active');

        // búsqueda por título o nombre de emprendedor
        if ($s = $r->query('search')) {
            $q->where(function($qb) use ($s) {
                $qb->where('title','like',"%{$s}%")
                   ->orWhereHas('company', fn($q2) =>
                       $q2->where('business_name','like',"%{$s}%")
                   );
            });
        }

        // filtros de categoría y comunidad (location)
        if ($cat = $r->query('category_id')) {
            $q->where('category_id', $cat);
        }
        if ($loc = $r->query('location_id')) {
            $q->where('location_id', $loc);
        }

        // paginación
        $perPage = (int) $r->query('per_page', 10);
        $pag = $q->with(['company','category','zone','media'])
                 ->orderBy('published_at','desc')
                 ->paginate($perPage);

        return response()->json($pag);
    }

    /**
     * 👁 Ver detalle de una experiencia (servicio)
     * GET /superadmin/experiencias/{experience}
     */
    public function show(Service $experience)
    {
        $experience->load([
            'company',
            'media',
            'category',
            'zone',
            'reviews',
            'reservations',
        ]);
        return response()->json($experience);
    }

    /**
     * ⏸ Pausar / reanudar
     * PUT /superadmin/experiencias/{experience}/pausar
     */
    public function pause(Service $experience)
    {
        $experience->status = $experience->status === 'active'
                             ? 'paused'
                             : 'active';
        $experience->save();

        return response()->json([
            'message' => 'Estado de la experiencia actualizado.',
            'status'  => $experience->status,
        ]);
    }

    /**
     * 🔄 Actualizar (edición ligera)
     * PUT /superadmin/experiencias/{experience}
     */
    public function update(Request $r, Service $experience)
    {
        $data = $r->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'location_id' => 'sometimes|exists:locations,id',
            'status'      => 'sometimes|in:active,paused',
        ]);

        $experience->update($data);

        return response()->json([
            'message'    => 'Experiencia actualizada correctamente.',
            'experience' => $experience->fresh()->load(['company','category','zone','media']),
        ]);
    }

    /**
     * 🗑️ Eliminar experiencia
     * DELETE /superadmin/experiencias/{experience}
     */
    public function destroy(Service $experience)
    {
        $experience->delete();
        return response()->json(['message'=>'Experiencia eliminada correctamente.']);
    }

    /**
     * 🎨 Listar categorías (para el filtro)
     * GET /superadmin/experiencias/categorias
     */
    public function categories()
    {
        return response()->json(Category::all());
    }

    /**
     * 🌍 Listar comunidades (locations)
     * GET /superadmin/comunidades
     */
    public function communities()
    {
        return response()->json(Location::all());
    }
}
