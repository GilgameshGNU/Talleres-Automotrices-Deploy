<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        return response()->json(Vehiculo::with(['cliente', 'modelo.marca'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idCliente' => 'required|integer|exists:cliente,idCliente',
            'idModelo' => 'required|integer|exists:modelovehiculo,idModelo',
            'placa' => 'required|string|max:15|unique:vehiculo,placa',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'kilometraje' => 'sometimes|integer|min:0',
        ]);

        $vehiculo = Vehiculo::create($validated);
        return response()->json($vehiculo, 201);
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with(['cliente', 'modelo.marca'])->find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }
        return response()->json($vehiculo);
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }

        $validated = $request->validate([
            'idCliente' => 'sometimes|required|integer|exists:cliente,idCliente',
            'idModelo' => 'sometimes|required|integer|exists:modelovehiculo,idModelo',
            'placa' => 'sometimes|required|string|max:15|unique:vehiculo,placa,' . $id . ',idVehiculo',
            'anio' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'kilometraje' => 'sometimes|integer|min:0',
        ]);

        $vehiculo->update($validated);
        return response()->json($vehiculo);
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }
        $vehiculo->delete();
        return response()->json(['message' => 'Vehículo eliminado con éxito']);
    }
}
