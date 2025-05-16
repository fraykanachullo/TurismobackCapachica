<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;



class UserManagementController extends Controller
{
    public function index()   { return User::with('roles')->get(); }
    public function store(Request $r) { /* crear turista o emprendedor */ }
    public function show($id) { return User::with('roles')->findOrFail($id); }
    public function update(Request $r, $id) { /* editar datos y roles */ }
    public function destroy($id) { User::findOrFail($id)->delete(); return response()->noContent(); }
}