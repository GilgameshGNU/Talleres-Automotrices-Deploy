<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleOrdenTrabajo;

class DetalleOrdenTrabajoController extends Controller
{
    public function index()
    {
        return response()->json(DetalleOrdenTrabajo::all());
    }

    public function store(Request $request)
    {
        $item = DetalleOrdenTrabajo::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = DetalleOrdenTrabajo::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = DetalleOrdenTrabajo::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = DetalleOrdenTrabajo::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
