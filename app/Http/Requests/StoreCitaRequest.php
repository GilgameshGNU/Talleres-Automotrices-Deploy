<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idCliente'  => 'required|integer|exists:cliente,idCliente',
            'idVehiculo' => 'required|integer|exists:vehiculo,idVehiculo',
            'idMecanico' => 'nullable|integer|exists:mecanico,idMecanico',
            'fecha'      => 'required|date|after_or_equal:today',
            'hora'       => 'required|date_format:H:i',
            'motivo'     => 'nullable|string|max:255',
        ];
    }
}
