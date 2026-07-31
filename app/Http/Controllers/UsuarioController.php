<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    protected $rolController;

    // Inject RolController to show internal dependency
    public function __construct(RolController $rolController)
    {
        $this->rolController = $rolController;
    }

    public function index()
    {
        return response()->json(Usuario::with('rol')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idRol' => 'required|integer|exists:rol,idRol',
            'nombreUsuario' => 'required|string|max:50|unique:usuario,nombreUsuario',
            'contrasena' => 'required|string|min:6|max:255',
            'email' => 'required|email|max:100|unique:usuario,email',
            'estado' => 'sometimes|integer|in:0,1',
            'created_at' => 'sometimes|date',
        ]);

        // Hash password
        $validated['contrasena'] = Hash::make($validated['contrasena']);

        $usuario = Usuario::create($validated);
        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with('rol')->find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'idRol' => 'sometimes|required|integer|exists:rol,idRol',
            'nombreUsuario' => 'sometimes|required|string|max:50|unique:usuario,nombreUsuario,' . $id . ',idUsuario',
            'contrasena' => 'sometimes|required|string|min:6|max:255',
            'email' => 'sometimes|required|email|max:100|unique:usuario,email,' . $id . ',idUsuario',
            'estado' => 'sometimes|integer|in:0,1',
            'created_at' => 'sometimes|date',
        ]);

        if (isset($validated['contrasena'])) {
            $validated['contrasena'] = Hash::make($validated['contrasena']);
        }

        $usuario->update($validated);
        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}
