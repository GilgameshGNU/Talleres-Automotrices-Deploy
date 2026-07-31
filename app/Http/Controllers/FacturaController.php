<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function index()
    {
        return response()->json(Factura::all());
    }

    public function store(Request $request)
    {
        $item = Factura::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Factura::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Factura::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Factura::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
