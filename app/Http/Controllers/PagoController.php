<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;

class PagoController extends Controller
{
    public function index()
    {
        return response()->json(Pago::all());
    }

    public function store(Request $request)
    {
        $item = Pago::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Pago::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Pago::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Pago::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
