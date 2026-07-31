<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostico;

class DiagnosticoController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnostico::query();
        if ($request->has('idOrden')) {
            $query->where('idOrden', $request->idOrden);
        }
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
        $especialidadesStr = '';
        if (isset($data['especialidades']) && is_array($data['especialidades'])) {
            $especialidadesStr = implode(', ', $data['especialidades']);
        }
        $severidad = $data['severidad'] ?? 'Media';
        
        // Guardamos todo en descripcion por si no existen las columnas en BD
        $descAdicional = "\n\nEspecialidades: {$especialidadesStr}\nSeveridad: {$severidad}";
        $data['descripcion'] = ($data['descripcion'] ?? '') . $descAdicional;
        
        $item = Diagnostico::create($data);
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Diagnostico::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Diagnostico::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Diagnostico::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
