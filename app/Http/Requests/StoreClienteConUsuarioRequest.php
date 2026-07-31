<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteConUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombreCompleto' => 'required|string|max:150',
            'ci_nit'         => 'required|string|max:20|unique:cliente,ci_nit',
            'telefono'       => 'required|string|max:20',
            'email'          => 'required|email|max:100|unique:usuario,email',
            'direccion'      => 'nullable|string|max:255',
            'password'       => 'nullable|string|min:4|max:100',
        ];
    }
}
