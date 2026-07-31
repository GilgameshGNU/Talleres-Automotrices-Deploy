<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenTrabajo;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenTrabajo::with([
            'cliente',
            'vehiculo.modelo.marca',
            'mecanico'
        ]);

        if ($request->query('disponibles')) {
            $query->whereNull('idMecanico')
                  ->where(function($q) {
                      $q->whereIn('estado', ['Recibido', 'recibido', 'Pendiente', 'pendiente', 'Disponible', 'disponible'])
                        ->orWhereNull('estado')
                        ->orWhere('estado', '');
                  });
        }

        if ($request->query('mis')) {
            $user = $request->user();
            if ($user) {
                $mecanico = \App\Models\Mecanico::where('idUsuario', $user->idUsuario)->first();
                if ($mecanico) {
                    $query->where('idMecanico', $mecanico->idMecanico);
                }
            }
        }

        $ordenes = $query->latest('idOrden')->get()->map(function ($orden) {
            if ($orden->vehiculo) {
                $orden->vehiculo->marca = $orden->vehiculo->modelo->marca->nombre ?? '';
                $orden->vehiculo->modelo_nombre = $orden->vehiculo->modelo->nombre ?? '';
                $orden->vehiculo->modelo = $orden->vehiculo->modelo_nombre;
            }
            return $orden;
        });

        if ($request->has('page')) {
            // Paginate is harder to map like this without losing pagination meta.
            // For now just return the mapped collection or rely on JS.
            return response()->json($query->latest('idOrden')->paginate(30)); 
        }

        return response()->json($ordenes);
    }

    public function store(\App\Http\Requests\StoreOrdenTrabajoRequest $request)
    {
        $validated = $request->validated();
        $validated['estado'] = 'Recibido'; // Por defecto al crear
        $validated['fechaIngreso'] = now();

        $item = OrdenTrabajo::create($validated);
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = OrdenTrabajo::with(['cliente', 'vehiculo.modelo.marca', 'mecanico'])->findOrFail($id);
        if ($item->vehiculo) {
            $item->vehiculo->marca = $item->vehiculo->modelo->marca->nombre ?? '';
            $item->vehiculo->modelo_nombre = $item->vehiculo->modelo->nombre ?? '';
            $item->vehiculo->modelo = $item->vehiculo->modelo_nombre;
        }
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = OrdenTrabajo::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = OrdenTrabajo::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    // FASE 1.A: Portal Recepcionista - Actualizar estado (ej. de Recibido a En reparación)
    public function updateEstado(\App\Http\Requests\UpdateEstadoOrdenRequest $request, $id)
    {
        $orden = OrdenTrabajo::findOrFail($id);
        $validated = $request->validated();
        
        $orden->estado = $validated['etapa'];
        $orden->etapa = $validated['etapa'];
        
        if ($validated['etapa'] === 'Terminado') {
            $orden->horaFinReal = now();
        }

        $orden->save();
        return response()->json($orden);
    }

    // FASE 2.A: Portal Mecánico - Aceptar orden (iniciar trabajo)
    public function aceptar(Request $request, $id)
    {
        $orden = OrdenTrabajo::findOrFail($id);
        
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $mecanico = \App\Models\Mecanico::where('idUsuario', $user->idUsuario)->first();
        if (!$mecanico) {
            // Auto-crear perfil de mecánico si el usuario tiene el rol pero no existe en la tabla mecanico
            $mecanico = \App\Models\Mecanico::create([
                'idUsuario' => $user->idUsuario,
                'nombreCompleto' => $user->nombreUsuario ?? 'Mecánico Auto',
                'ci' => 'CI-' . $user->idUsuario . rand(100,999),
                'idSucursal' => 1, // Por defecto
                'disponible' => 1
            ]);
        }

        // Opcional: validar que no tenga otra orden activa o que esté disponible
        if ($orden->idMecanico && $orden->idMecanico !== $mecanico->idMecanico) {
            return response()->json(['message' => 'La orden ya está asignada a otro mecánico'], 400);
        }

        $orden->idMecanico = $mecanico->idMecanico;
        $orden->estado = 'En reparación';
        $orden->horaInicio = now();
        $orden->save();

        return response()->json([
            'message' => 'Orden aceptada e iniciada',
            'orden' => $orden
        ]);
    }
}
