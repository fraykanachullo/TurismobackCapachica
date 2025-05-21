<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return Auth::user();
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'avatar_url' => 'sometimes|url',
            // otras validaciones que necesites
        ]);

        $user->update($request->all());

        return response()->json(['message' => 'Perfil actualizado', 'user' => $user]);
    }
}
