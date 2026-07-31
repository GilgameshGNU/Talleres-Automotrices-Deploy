<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioController extends Controller
{
    public function index()
    {
        return response()->json(
            MovimientoInventario::with('inventario.repuesto')->orderBy('fecha', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        // Soporte para request desde el portal de mecánico
        if ($request->tipo === 'uso') {
            $request->merge([
                'tipo' => 'Salida',
                'motivo' => 'Uso en orden de trabajo #' . $request->idOrden
            ]);
            if ($request->has('idRepuesto')) {
                $inventario = Inventario::where('idRepuesto', $request->idRepuesto)->first();
                if ($inventario) {
                    $request->merge(['idInventario' => $inventario->idInventario]);
                }
            }
        }

        $validated = $request->validate([
            'idInventario' => 'required|integer|exists:inventario,idInventario',
            'tipo' => 'required|in:Entrada,Salida,Ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        $movimiento = DB::transaction(function () use ($validated) {
            $inventario = Inventario::lockForUpdate()->findOrFail($validated['idInventario']);

            if ($validated['tipo'] === 'Entrada') {
                $inventario->stockActual += $validated['cantidad'];
            } elseif ($validated['tipo'] === 'Salida') {
                if ($inventario->stockActual < $validated['cantidad']) {
                    abort(422, 'Stock insuficiente para registrar la salida.');
                }
                $inventario->stockActual -= $validated['cantidad'];
            } else {
                // Ajuste: establece el stock al valor indicado
                $inventario->stockActual = $validated['cantidad'];
            }

            $inventario->save();

            return MovimientoInventario::create($validated);
        });

        return response()->json($movimiento->load('inventario.repuesto'), 201);
    }

    public function show($id)
    {
        $movimiento = MovimientoInventario::with('inventario.repuesto')->find($id);
        if (!$movimiento) {
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }
        return response()->json($movimiento);
    }

    public function destroy($id)
    {
        $result = DB::transaction(function () use ($id) {
            $movimiento = MovimientoInventario::find($id);
            if (!$movimiento) {
                return null;
            }

            $inventario = Inventario::lockForUpdate()->find($movimiento->idInventario);
            if ($inventario) {
                if ($movimiento->tipo === 'Entrada') {
                    $inventario->stockActual -= $movimiento->cantidad;
                } elseif ($movimiento->tipo === 'Salida') {
                    $inventario->stockActual += $movimiento->cantidad;
                }
                // Los ajustes no se revierten automáticamente: el valor previo no queda registrado.
                $inventario->stockActual = max(0, $inventario->stockActual);
                $inventario->save();
            }

            $movimiento->delete();
            return true;
        });

        if (!$result) {
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }

        return response()->json(['message' => 'Movimiento eliminado y stock revertido con éxito']);
    }
}
