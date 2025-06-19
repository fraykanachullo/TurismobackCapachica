<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:superadmin']);
    }

    /**
     * Listar todas las categorías
     * GET /superadmin/experiencias/categorias
     */
    public function index()
    {
        return response()->json(Category::all());
    }

    /**
     * Crear una nueva categoría
     * POST /superadmin/experiencias/categorias
     */
    public function store(Request $r)
    {
        $r->validate([
            'name'        => 'required|string|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,inactive',
        ]);
    
        $cat = Category::create($r->only(['name','description','status']));
    
        return response()->json([
            'message'  => 'Categoría creada con éxito.',
            'category' => $cat
        ], 201);
    }

    /**
     * Actualizar una categoría
     * PUT /superadmin/experiencias/categorias/{category}
     */
    public function update(Request $r, Category $category)
        {
            $r->validate([
                'name'        => 'required|string|unique:categories,name,'.$category->id,
                'description' => 'nullable|string|max:1000',
                'status'      => 'required|in:active,inactive',
            ]);

            $category->update($r->only(['name','description','status']));

            return response()->json([
                'message'  => 'Categoría actualizada correctamente.',
                'category' => $category
            ]);
        }


    /**
     * Eliminar una categoría
     * DELETE /superadmin/experiencias/categorias/{category}
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }
}
