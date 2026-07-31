<?php

namespace App\Http\Controllers;

use App\Models\TipoServicio;
use Illuminate\Http\Request;

class TipoServicioController extends Controller
{
    public function index()
    {
        return response()->json(TipoServicio::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipoServicio = TipoServicio::create($validated);
        return response()->json($tipoServicio, 201);
    }

    public function show($id)
    {
        $tipoServicio = TipoServicio::find($id);
        if (!$tipoServicio) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }
        return response()->json($tipoServicio);
    }

    public function update(Request $request, $id)
    {
        $tipoServicio = TipoServicio::find($id);
        if (!$tipoServicio) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipoServicio->update($validated);
        return response()->json($tipoServicio);
    }

    public function destroy($id)
    {
        $tipoServicio = TipoServicio::find($id);
        if (!$tipoServicio) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }
        $tipoServicio->delete();
        return response()->json(['message' => 'Tipo de servicio eliminado con éxito']);
    }
}
