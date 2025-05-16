<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;


class ExperienceManagementController extends Controller
{
    public function index()
    {
        return Service::with(['company','category','location'])->get();
    }

    public function show($id)
    {
        return Service::with(['company','media'])->findOrFail($id);
    }

    public function update(Request $r, $id)
    {
        $s = Service::findOrFail($id);
        $s->update($r->only(['status','price','title','description']));
        return response()->json(['message'=>'Servicio actualizado']);
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return response()->noContent();
    }
}
