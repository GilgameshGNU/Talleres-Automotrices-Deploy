<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        return response()->json(Inventario::with(['sucursal', 'repuesto'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idSucursal' => 'required|integer|exists:sucursal,idSucursal',
            'idRepuesto' => 'required|integer|exists:repuesto,idRepuesto|unique:inventario,idRepuesto,NULL,idInventario,idSucursal,' . $request->idSucursal,
            'stockActual' => 'sometimes|integer|min:0',
            'stockMinimo' => 'sometimes|integer|min:0',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        $inventario = Inventario::create($validated);
        return response()->json($inventario->load(['sucursal', 'repuesto']), 201);
    }

    public function show($id)
    {
        $inventario = Inventario::with(['sucursal', 'repuesto', 'movimientos'])->find($id);
        if (!$inventario) {
            return response()->json(['message' => 'Inventario no encontrado'], 404);
        }
        return response()->json($inventario);
    }

    public function update(Request $request, $id)
    {
        $inventario = Inventario::find($id);
        if (!$inventario) {
            return response()->json(['message' => 'Inventario no encontrado'], 404);
        }

        $validated = $request->validate([
            'idSucursal' => 'sometimes|required|integer|exists:sucursal,idSucursal',
            'idRepuesto' => 'sometimes|required|integer|exists:repuesto,idRepuesto',
            'stockActual' => 'sometimes|integer|min:0',
            'stockMinimo' => 'sometimes|integer|min:0',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        $inventario->update($validated);
        return response()->json($inventario->load(['sucursal', 'repuesto']));
    }

    public function destroy($id)
    {
        $inventario = Inventario::find($id);
        if (!$inventario) {
            return response()->json(['message' => 'Inventario no encontrado'], 404);
        }
        $inventario->delete();
        return response()->json(['message' => 'Registro de inventario eliminado con éxito']);
    }
}
