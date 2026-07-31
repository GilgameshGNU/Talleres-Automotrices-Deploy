<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use Illuminate\Http\Request;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Repuesto::with(['proveedor', 'inventarios']);
        
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
        }

        $repuestos = $query->get()->map(function ($repuesto) {
            $repuesto->stockActual = $repuesto->inventarios->sum('stockActual');
            $repuesto->precio = $repuesto->precioVenta;
            return $repuesto;
        });

        return response()->json($repuestos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idProveedor' => 'nullable|integer|exists:proveedor,idProveedor',
            'codigo' => 'required|string|max:50|unique:repuesto,codigo',
            'nombre' => 'required|string|max:150',
            'precioVenta' => 'required|numeric|min:0',
            'marca' => 'nullable|string|max:50',
        ]);

        $repuesto = Repuesto::create($validated);
        return response()->json($repuesto->load('proveedor'), 201);
    }

    public function show($id)
    {
        $repuesto = Repuesto::with('proveedor')->find($id);
        if (!$repuesto) {
            return response()->json(['message' => 'Repuesto no encontrado'], 404);
        }
        return response()->json($repuesto);
    }

    public function update(Request $request, $id)
    {
        $repuesto = Repuesto::find($id);
        if (!$repuesto) {
            return response()->json(['message' => 'Repuesto no encontrado'], 404);
        }

        $validated = $request->validate([
            'idProveedor' => 'nullable|integer|exists:proveedor,idProveedor',
            'codigo' => 'sometimes|required|string|max:50|unique:repuesto,codigo,' . $id . ',idRepuesto',
            'nombre' => 'sometimes|required|string|max:150',
            'precioVenta' => 'sometimes|required|numeric|min:0',
            'marca' => 'nullable|string|max:50',
        ]);

        $repuesto->update($validated);
        return response()->json($repuesto->load('proveedor'));
    }

    public function destroy($id)
    {
        $repuesto = Repuesto::find($id);
        if (!$repuesto) {
            return response()->json(['message' => 'Repuesto no encontrado'], 404);
        }
        $repuesto->delete();
        return response()->json(['message' => 'Repuesto eliminado con éxito']);
    }
}
