<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Blog;         // ejemplar
use App\Models\Banner;       // ejemplar
use App\Models\Testimonial;  // ejemplar


class ContentManagementController extends Controller
{
    public function index()
    {
        // combinar blogs, banners y testimonios
        return [
          'blogs'        => Blog::all(),
          'banners'      => Banner::all(),
          'testimonials' => Testimonial::all(),
        ];
    }

    public function show($id)    { /* según tipo */ }
    public function update(Request $r, $id) { /* aprobar/editar */ }
    public function destroy($id) { /* borrar contenido */ }
}
