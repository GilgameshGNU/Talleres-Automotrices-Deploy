<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    protected $tipoServicioController;

    // Inject TipoServicioController to show internal dependency
    public function __construct(TipoServicioController $tipoServicioController)
    {
        $this->tipoServicioController = $tipoServicioController;
    }

    public function index()
    {
        return response()->json(Servicio::with('tipoServicio')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idTipoServicio' => 'required|integer|exists:tiposervicio,idTipoServicio',
            'nombre' => 'required|string|max:150',
            'precioBase' => 'required|numeric|min:0',
            'duracionEstimada' => 'nullable|integer|min:0',
        ]);

        $servicio = Servicio::create($validated);
        return response()->json($servicio, 201);
    }

    public function show($id)
    {
        $servicio = Servicio::with('tipoServicio')->find($id);
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }
        return response()->json($servicio);
    }

    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $validated = $request->validate([
            'idTipoServicio' => 'sometimes|required|integer|exists:tiposervicio,idTipoServicio',
            'nombre' => 'sometimes|required|string|max:150',
            'precioBase' => 'sometimes|required|numeric|min:0',
            'duracionEstimada' => 'nullable|integer|min:0',
        ]);

        $servicio->update($validated);
        return response()->json($servicio);
    }

    public function destroy($id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }
        $servicio->delete();
        return response()->json(['message' => 'Servicio eliminado con éxito']);
    }
}
