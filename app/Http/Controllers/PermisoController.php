<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        return response()->json(Permiso::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idRol' => 'required|integer|exists:rol,idRol',
            'nombre' => 'required|string|max:100',
            'modulo' => 'required|string|max:50',
        ]);

        $permiso = Permiso::create($validated);
        return response()->json($permiso, 201);
    }

    public function show($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permiso);
    }

    public function update(Request $request, $id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $validated = $request->validate([
            'idRol' => 'sometimes|required|integer|exists:rol,idRol',
            'nombre' => 'sometimes|required|string|max:100',
            'modulo' => 'sometimes|required|string|max:50',
        ]);

        $permiso->update($validated);
        return response()->json($permiso);
    }

    public function destroy($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        $permiso->delete();
        return response()->json(['message' => 'Permiso eliminado con éxito']);
    }
}
