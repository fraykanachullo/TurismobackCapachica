<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:emprendedor']);
    }

    /**
     * Listar posts del emprendedor
     */
    public function index()
    {
        $company = Auth::user()->company;
        $posts = Blog::where('company_id', $company->id)
                     ->orderBy('created_at','desc')
                     ->get();

        return response()->json($posts);
    }

    /**
     * Crear un nuevo post
     */
    public function store(Request $request)
    {
        $company = Auth::user()->company;

        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'content'             => 'required|string',
            'featured_image_url'  => 'nullable|url',
            'status'              => 'nullable|in:draft,published',
            'published_at'        => 'nullable|date',
        ]);

        $post = Blog::create([
            'company_id'         => $company->id,
            'title'              => $data['title'],
            'content'            => $data['content'],
            'featured_image_url' => $data['featured_image_url'] ?? null,
            'status'             => $data['status']             ?? 'draft',
            'published_at'       => $data['published_at']       ?? now(),
        ]);

        return response()->json([
            'message' => 'Post creado exitosamente.',
            'post'    => $post,
        ], 201);
    }

    /**
     * Ver un post
     */
    public function show($id)
    {
        $post = Blog::findOrFail($id);
        if ($post->company_id !== Auth::user()->company->id) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        return response()->json($post);
    }

    /**
     * Actualizar un post
     */
    public function update(Request $request, $id)
    {
        $post = Blog::findOrFail($id);
        if ($post->company_id !== Auth::user()->company->id) {
            return response()->json(['message'=>'No autorizado'], 403);
        }

        $data = $request->validate([
            'title'               => 'sometimes|required|string|max:255',
            'content'             => 'sometimes|required|string',
            'featured_image_url'  => 'sometimes|nullable|url',
            'status'              => 'sometimes|required|in:draft,published',
            'published_at'        => 'sometimes|nullable|date',
        ]);

        $post->update($data);

        return response()->json([
            'message' => 'Post actualizado.',
            'post'    => $post->fresh(),
        ]);
    }

    /**
     * Eliminar un post
     */
    public function destroy($id)
    {
        $post = Blog::findOrFail($id);
        if ($post->company_id !== Auth::user()->company->id) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        $post->delete();
        return response()->noContent();
    }
}
