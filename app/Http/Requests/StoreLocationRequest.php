<?php
// app/Http/Requests/StoreLocationRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('superadmin');
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255|unique:locations,name',
            'type'               => 'required|in:comunidad,centro_poblado',
            'descripcion_corta'  => 'nullable|string|max:500',
            'descripcion_larga'  => 'nullable|string',
            'atractivos'         => 'nullable|string',
            'habitantes'         => 'nullable|integer|min:0',
            'estado'             => 'required|in:activa,inactiva',
            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'galeria'            => 'nullable|array|max:5',
            'galeria.*'          => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }
}
