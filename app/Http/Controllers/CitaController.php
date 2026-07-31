<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        return response()->json(Cita::with(['cliente', 'vehiculo', 'mecanico'])->orderBy('fecha', 'desc')->get());
    }

    public function store(\App\Http\Requests\StoreCitaRequest $request)
    {
        $validated = $request->validated();
        $validated['estado'] = 'Pendiente'; // default value or from request if needed

        $cita = Cita::create($validated);
        return response()->json($cita, 201);
    }

    // FASE 1.A: Citas de hoy
    public function hoy()
    {
        $citas = Cita::with(['cliente', 'vehiculo', 'mecanico'])
            ->whereDate('fecha', now()->toDateString())
            ->orderBy('hora', 'asc')
            ->get();
        return response()->json($citas);
    }

    public function show($id)
    {
        $cita = Cita::with(['cliente', 'vehiculo', 'mecanico'])->find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        return response()->json($cita);
    }

    public function update(Request $request, $id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $validated = $request->validate([
            'idCliente' => 'sometimes|required|integer|exists:cliente,idCliente',
            'idVehiculo' => 'sometimes|required|integer|exists:vehiculo,idVehiculo',
            'idMecanico' => 'nullable|integer|exists:mecanico,idMecanico',
            'fecha' => 'sometimes|required|date',
            'hora' => 'sometimes|required|date_format:H:i',
            'estado' => 'sometimes|in:Pendiente,Confirmada,Cancelada,Completada',
            'motivo' => 'nullable|string|max:255',
        ]);

        $cita->update($validated);
        return response()->json($cita);
    }

    public function destroy($id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        $cita->delete();
        return response()->json(['message' => 'Cita eliminada con éxito']);
    }
}
