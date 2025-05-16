<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // Redirigir al usuario a Google para iniciar sesión
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Manejar la respuesta de Google y registrar o autenticar al usuario
    public function handleGoogleCallback()
    {
        // Obtener los datos del usuario de Google
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Verificar si el usuario ya existe con google_id o email
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            // Si el usuario no existe, crear uno nuevo
            $user = User::create([
                'google_id' => $googleUser->getId(), // Guardar el google_id
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(16)),  // Asignar una contraseña aleatoria
            ]);
        } else {
            // Si el usuario ya existe, solo actualiza el google_id si es necesario
            $user->google_id = $googleUser->getId();
            $user->save();
        }


         // Asignar el rol de turista si el usuario no tiene un rol
            if ($user->roles->isEmpty()) {
                $user->assignRole('turista');  // Asignamos automáticamente el rol de turista
            }
        // Autenticar al usuario
        Auth::login($user, true);

         // Verifica el rol y redirige
    if ($user->hasRole('superadmin')) {
        return redirect('http://localhost:4200/admin/dashboard');
    } elseif ($user->hasRole('emprendedor')) {
        return redirect('http://localhost:4200/emprendedor/dashboard');
    } elseif ($user->hasRole('turista')) {
        return redirect('http://localhost:4200/turista/perfil');
    } 
    }
}


