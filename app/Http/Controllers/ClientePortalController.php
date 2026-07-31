<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\OrdenTrabajo;
use App\Models\TipoServicio;
use App\Models\Usuario;
use Carbon\Carbon;

class ClientePortalController extends Controller
{
    private function getCliente()
    {
        $user = Auth::user();
        if (!$user) return null;
        
        $cliente = Cliente::where('idUsuario', $user->idUsuario)->first();
        if (!$cliente) {
            $user->load('rol');
            if ($user->rol && $user->rol->nombre === 'Cliente') {
                $cliente = Cliente::create([
                    'idUsuario'      => $user->idUsuario,
                    'nombreCompleto' => $user->nombreUsuario,
                    'telefono'       => '',
                    'direccion'      => ''
                ]);
            }
        }
        return $cliente;
    }

    public function getProfile()
    {
        $cliente = $this->getCliente();
        if (!$cliente) {
            return response()->json(['message' => 'No autorizado o no es cliente'], 403);
        }

        $user = Auth::user();

        return response()->json([
            'cliente' => $cliente,
            'usuario' => [
                'email' => $user->email,
                'created_at' => clone $user->created_at
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'nombreCompleto' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'required|email|max:100',
            'contrasena' => 'nullable|string|min:6'
        ]);

        $cliente->nombreCompleto = $request->nombreCompleto;
        if ($request->has('telefono')) $cliente->telefono = $request->telefono;
        if ($request->has('direccion')) $cliente->direccion = $request->direccion;
        $cliente->save();

        $user = Auth::user();
        
        // Update email if different
        if ($user->email !== $request->email) {
            $existingUser = Usuario::where('email', $request->email)->where('idUsuario', '!=', $user->idUsuario)->first();
            if ($existingUser) {
                return response()->json(['message' => 'El correo electr-nico ya est- en uso'], 400);
            }
            $user->email = $request->email;
        }

        if ($request->filled('contrasena')) {
            $user->contrasena = Hash::make($request->contrasena);
        }
        
        $user->save();

        return response()->json(['message' => 'Perfil actualizado exitosamente']);
    }

    public function getCatalogo()
    {
        // Traer servicios con su tipo de servicio
        $servicios = Servicio::with('tipoServicio')->get();
        return response()->json($servicios);
    }

    public function getSolicitudes()
    {
        $cliente = $this->getCliente();
        if (!$cliente) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Ya que usamos ordentrabajo, las traeremos y si la fecha de ingreso es el mismo día, asumimos fechaSolicitud
        $ordenes = OrdenTrabajo::where('idCliente', $cliente->idCliente)
                    ->orderBy('idOrden', 'desc')
                    ->get();

        return response()->json($ordenes);
    }

    public function createSolicitud(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'idServicio' => 'required|integer',
            'precioEstimado' => 'required|numeric'
        ]);

        $servicio = Servicio::find($request->idServicio);
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        // Crear Orden de Trabajo sin Vehículo ni Mec-nico
        $orden = new OrdenTrabajo();
        $orden->idCliente = $cliente->idCliente;
        $orden->fechaIngreso = date('Y-m-d');
        $orden->estado = 'Pendiente';
        $orden->diagnostico = 'Servicio solicitado: ' . $servicio->nombre;
        $orden->total = $request->precioEstimado;
        $orden->save();

        return response()->json(['message' => 'Solicitud creada con éxito', 'orden' => $orden], 201);
    }

    // FASE 3.A: Portal Cliente - Estado actual de sus vehículos en taller
    public function getEstadoVehiculo()
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'No autorizado'], 403);

        $ordenesActivas = OrdenTrabajo::with(['vehiculo', 'mecanico'])
            ->where('idCliente', $cliente->idCliente)
            ->whereNotIn('estado', ['Completado', 'Terminado', 'Cancelada'])
            ->get()
            ->map(function ($orden) {
                return [
                    'idOrden' => $orden->idOrden,
                    'placa' => $orden->vehiculo ? $orden->vehiculo->placa : 'Sin veh-culo',
                    'estado' => $orden->estado,
                    'mecanico' => $orden->mecanico ? $orden->mecanico->nombreCompleto : 'Sin asignar',
                    'fechaIngreso' => $orden->fechaIngreso,
                    'progreso' => $this->calcularProgreso($orden->estado)
                ];
            });

        return response()->json($ordenesActivas);
    }

    private function calcularProgreso($estado)
    {
        return match ($estado) {
            'Recibido' => 25,
            'Diagnóstico' => 50,
            'En reparaci-n' => 75,
            'Terminado', 'Completado' => 100,
            default => 0,
        };
    }

    // FASE 3.A: Portal Cliente - Historial de servicios pasados
    public function getHistorial()
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'No autorizado'], 403);

        $historial = OrdenTrabajo::with(['vehiculo', 'detalles.servicio'])
            ->where('idCliente', $cliente->idCliente)
            ->whereIn('estado', ['Completado', 'Terminado'])
            ->orderBy('fechaSalida', 'desc')
            ->get();

        return response()->json($historial);
    }

    // FASE 3.A: Portal Cliente - Sus notificaciones
    public function getNotificacionesCliente()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $notificaciones = \App\Models\Notificacion::where('idUsuario', $user->idUsuario)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($notificaciones);
    }
}
