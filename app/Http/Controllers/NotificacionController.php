<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function index()
    {
        return response()->json(Notificacion::all());
    }

    public function store(Request $request)
    {
        $item = Notificacion::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Notificacion::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Notificacion::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Notificacion::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    // FASE 1.A: Portal Recepcionista - Notificaciones no leídas
    public function noLeidas(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $notificaciones = Notificacion::where('idUsuario', $user->idUsuario)
            ->where('leido', false)
            ->orderBy('fecha', 'desc')
            ->get();
            
        return response()->json($notificaciones);
    }
}
