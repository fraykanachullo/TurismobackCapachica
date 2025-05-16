<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;


class CompanyManagementController extends Controller
{
    public function pending()
    {
        return Company::where('status','pendiente')->with('user')->get();
    }

    public function approve($id)
    {
        $c = Company::findOrFail($id);
        $c->status='aprobada'; $c->save();
        return response()->json(['message'=>'Empresa aprobada']);
    }

    public function reject($id)
    {
        $c = Company::findOrFail($id);
        $c->status='rechazada'; $c->save();
        return response()->json(['message'=>'Empresa rechazada']);
    }
}