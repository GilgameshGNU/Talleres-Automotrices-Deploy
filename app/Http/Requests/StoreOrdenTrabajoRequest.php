<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenTrabajoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idCliente'          => 'required|integer|exists:cliente,idCliente',
            'idVehiculo'         => 'required|integer|exists:vehiculo,idVehiculo',
            'idMecanico'         => 'nullable|integer|exists:mecanico,idMecanico',
            'servicioSolicitado' => 'required|string|max:500',
            'sucursal'           => 'nullable|string|max:100',
            'horaFinEstimada'    => 'nullable|date|after:now',
        ];
    }
}
