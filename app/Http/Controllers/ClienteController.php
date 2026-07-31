<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return response()->json(
                Cliente::select(['idCliente', 'idUsuario', 'nombreCompleto', 'ci_nit', 'telefono', 'direccion', 'created_at'])
                    ->withCount('vehiculos')
                    ->latest('idCliente')
                    ->paginate(50)
            );
        }

        return response()->json(
            Cliente::select(['idCliente', 'idUsuario', 'nombreCompleto', 'ci_nit', 'telefono', 'direccion', 'created_at'])
                ->withCount('vehiculos')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreCompleto' => 'required|string|max:150',
            'ci_nit' => 'required|string|max:20|unique:cliente,ci_nit',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::with('vehiculos')->find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombreCompleto' => 'sometimes|required|string|max:150',
            'ci_nit' => 'sometimes|required|string|max:20|unique:cliente,ci_nit,' . $id . ',idCliente',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente->update($validated);
        return response()->json($cliente);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado con éxito']);
    }

    // FASE 1.A: Portal Recepcionista - Buscar cliente rápido
    public function buscar(Request $request)
    {
        $q = trim($request->query('q', ''));

        $query = Cliente::with(['usuario' => function ($u) {
            $u->select('idUsuario', 'nombreUsuario', 'email', 'estado');
        }]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('ci_nit', 'LIKE', "{$q}%")
                    ->orWhere('ci_nit', 'LIKE', "%{$q}%")
                    ->orWhere('telefono', 'LIKE', "{$q}%")
                    ->orWhere('nombreCompleto', 'LIKE', "%{$q}%")
                    ->orWhereHas('usuario', function ($u) use ($q) {
                        $u->where('email', 'LIKE', "%{$q}%")
                          ->orWhere('nombreUsuario', 'LIKE', "%{$q}%");
                    });
            });
        }

        $clientes = $query->select(['idCliente', 'idUsuario', 'nombreCompleto', 'ci_nit', 'telefono', 'direccion'])
            ->take(50)
            ->get();

        return response()->json($clientes);
    }

    // FASE 1.A: Portal Recepcionista - Crear cliente y usuario simultáneamente
    public function storeConUsuario(\App\Http\Requests\StoreClienteConUsuarioRequest $request)
    {
        $validated = $request->validated();
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Asignar rol Cliente (asumimos que existe un rol con nombre 'Cliente')
            $rolClienteId = \Illuminate\Support\Facades\Cache::remember('rol_id_cliente', 86400, function() {
                return \App\Models\Rol::where('nombre', 'Cliente')->value('idRol');
            });

            if (!$rolClienteId) {
                throw new \Exception('Rol Cliente no encontrado.');
            }

            // Usar la contraseña enviada en el formulario o por defecto su CI/NIT
            $rawPassword = !empty($validated['password']) ? $validated['password'] : $validated['ci_nit'];
            $password = bcrypt($rawPassword);

            $usuario = \App\Models\Usuario::create([
                'idRol' => $rolClienteId,
                'nombreUsuario' => strtolower(str_replace(' ', '', $validated['nombreCompleto'])) . rand(10,99),
                'email' => $validated['email'],
                'contrasena' => $password,
                'estado' => 1
            ]);

            $cliente = Cliente::create([
                'idUsuario' => $usuario->idUsuario,
                'nombreCompleto' => $validated['nombreCompleto'],
                'ci_nit' => $validated['ci_nit'],
                'telefono' => $validated['telefono'],
                'direccion' => $validated['direccion'] ?? null,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json([
                'cliente' => $cliente, 
                'usuario' => $usuario,
                'passwordPlano' => $rawPassword,
                'message' => 'Cliente y usuario creados con éxito'
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['message' => 'Error al crear cliente: ' . $e->getMessage()], 500);
        }
    }
}
