<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    protected $permisoController;

    // Inject PermisoController to show internal dependency
    public function __construct(PermisoController $permisoController)
    {
        $this->permisoController = $permisoController;
    }

    public function index()
    {
        return response()->json(Rol::withCount(['permisos', 'usuarios'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol = Rol::create($validated);
        return response()->json($rol, 201);
    }

    public function show($id)
    {
        $rol = Rol::with('permisos')->find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return response()->json($rol);
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol->update($validated);
        return response()->json($rol);
    }

    public function destroy($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        $rol->delete();
        return response()->json(['message' => 'Rol eliminado con éxito']);
    }

    /**
     * Get permisos for this role using the injected PermisoController.
     */
    public function getRolPermisos($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        // Retrieve permissions through the model relationship
        return response()->json($rol->permisos);
    }
}
