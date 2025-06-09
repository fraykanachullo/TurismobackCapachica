<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('superadmin');
    }

    public function rules(): array
    {
        // $this->route('community') es tu Location inyectada
        $id = $this->route('community')->id;

        return [
            'name'               => "sometimes|string|max:255|unique:locations,name,{$id}",
            'descripcion_corta'  => 'nullable|string|max:500',
            'descripcion_larga'  => 'nullable|string',
            'atractivos'         => 'nullable|string',
            'habitantes'         => 'nullable|integer|min:0',

            // **aquí estaba el fallo**:
            'estado'             => 'sometimes|in:activa,inactiva',

            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'galeria'            => 'nullable|array|max:5',
            'galeria.*'          => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }
}
