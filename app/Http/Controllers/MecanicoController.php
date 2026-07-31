<?php

namespace App\Http\Controllers;

use App\Models\Mecanico;
use Illuminate\Http\Request;

class MecanicoController extends Controller
{
    public function index()
    {
        return response()->json(Mecanico::with(['sucursal', 'especialidades'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreCompleto' => 'required|string|max:150',
            'ci' => 'required|string|max:20|unique:mecanico,ci',
            'telefono' => 'nullable|string|max:20',
            'idSucursal' => 'required|integer|exists:sucursal,idSucursal',
            'especialidades' => 'sometimes|array',
            'especialidades.*' => 'integer|exists:especialidad,idEspecialidad',
        ]);

        $mecanico = Mecanico::create($validated);
        $mecanico->especialidades()->sync($validated['especialidades'] ?? []);

        return response()->json($mecanico->load(['sucursal', 'especialidades']), 201);
    }

    public function show($id)
    {
        $mecanico = Mecanico::with(['sucursal', 'especialidades'])->find($id);
        if (!$mecanico) {
            return response()->json(['message' => 'Mecánico no encontrado'], 404);
        }
        return response()->json($mecanico);
    }

    public function update(Request $request, $id)
    {
        $mecanico = Mecanico::find($id);
        if (!$mecanico) {
            return response()->json(['message' => 'Mecánico no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombreCompleto' => 'sometimes|required|string|max:150',
            'ci' => 'sometimes|required|string|max:20|unique:mecanico,ci,' . $id . ',idMecanico',
            'telefono' => 'nullable|string|max:20',
            'idSucursal' => 'sometimes|required|integer|exists:sucursal,idSucursal',
            'especialidades' => 'sometimes|array',
            'especialidades.*' => 'integer|exists:especialidad,idEspecialidad',
        ]);

        $mecanico->update($validated);
        if (array_key_exists('especialidades', $validated)) {
            $mecanico->especialidades()->sync($validated['especialidades']);
        }

        return response()->json($mecanico->load(['sucursal', 'especialidades']));
    }

    public function destroy($id)
    {
        $mecanico = Mecanico::find($id);
        if (!$mecanico) {
            return response()->json(['message' => 'Mecánico no encontrado'], 404);
        }
        $mecanico->delete();
        return response()->json(['message' => 'Mecánico eliminado con éxito']);
    }

    // FASE 1.A: Mecánicos disponibles con su carga de trabajo (órdenes En reparación)
    public function conCarga()
    {
        $mecanicos = Mecanico::with(['especialidades'])
            ->where('disponible', 1)
            ->withCount(['ordenesTrabajo as ordenes_activas_count' => function ($query) {
                $query->where('estado', 'En reparación');
            }])
            ->get();
        return response()->json($mecanicos);
    }

    // FASE 2.A: Toggle de disponibilidad del mecánico logueado
    public function toggleDisponible(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $mecanico = Mecanico::where('idUsuario', $user->idUsuario)->first();
        if (!$mecanico) {
            $mecanico = Mecanico::create([
                'idUsuario' => $user->idUsuario,
                'nombreCompleto' => $user->nombreUsuario ?? 'Mecánico Auto',
                'ci' => 'CI-' . $user->idUsuario . rand(100,999),
                'idSucursal' => 1,
                'disponible' => 1
            ]);
        }

        $mecanico->disponible = !$mecanico->disponible;
        $mecanico->save();

        return response()->json([
            'message' => 'Disponibilidad actualizada',
            'disponible' => $mecanico->disponible
        ]);
    }
}
