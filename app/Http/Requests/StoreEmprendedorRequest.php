<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmprendedorRequest extends FormRequest
{
    /**
     * ¿El usuario está autorizado a hacer esta petición?
     */
    public function authorize(): bool
    {
        // Solo superadmins pueden
        return $this->user()?->hasRole('superadmin');
    }

    /**
     * Reglas de validación para crear un emprendedor
     */
    public function rules(): array
    {
        return [
            'nombre'                 => ['required','string','max:255'],
            'email'                  => ['required','email','max:255','unique:users,email'],
            'password'               => ['required','string','confirmed','min:6'],
            'password_confirmation'  => ['required','string','min:6'],
           // 'estado'                 => ['nullable','in:activo,pendiente,suspendido'],

            'imagen'                 => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:5048'],
        ];
    }

    /**
     * Mensajes personalizados (opcional)
     */
    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'email.unique'      => 'Este correo ya está registrado.',
            'password.confirmed'=> 'La confirmación de contraseña no coincide.',
        ];
    }
}
