<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registro de turistas.
     */
    public function registerTurista(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol TURISTA al usuario
        $user->assignRole('turista');

        return response()->json([
            'usuario' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->getRoleNames()->first()
            ],
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }

    /**
     * Inicio de sesión.
     */
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credenciales)) {
            return response()->json(['mensaje' => 'Credenciales inválidas'], 401);
        }

        $usuario = auth()->user();

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'rol' => $usuario->getRoleNames()->first(),
                'empresa' => $usuario->company, // ahora sí devuelve objeto o null
            ],
            'token' => $usuario->createToken('api-token')->plainTextToken,
        ]);
    }

    /**
     * Cierre de sesión (revoca tokens).
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada correctamente']);
    }

    /**
     * Obtener usuario autenticado.
     */
    public function user(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'id' => $usuario->id,
            'name' => $usuario->name,
            'email' => $usuario->email,
            'rol' => $usuario->getRoleNames()->first()
        ]);
    }
}
