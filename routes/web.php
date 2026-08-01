<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TipoServicioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MecanicoController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\DetalleOrdenTrabajoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ReporteController;

// ─────────────────────────────────────────────────────────────────
// SPA Entry Points — devuelven el blade principal (welcome.blade)
// ─────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// Ruta /login: si ya está autenticado redirige al inicio
Route::get('/login', function () {
    return view('welcome');
});



// Rutas SPA del panel administrativo
Route::get('/{view}', function () {
    return view('welcome');
})->where('view', 'panel|usuarios|roles|permisos|servicios|tipos-servicio|proveedores|clientes|vehiculos|citas|mecanicos|repuestos|inventario|movimientos-inventario|ordenes-trabajo|diagnosticos|detalles-orden|notificaciones|pagos|facturas|reportes');

// ─────────────────────────────────────────────────────────────────
// API Routes
// ─────────────────────────────────────────────────────────────────
Route::prefix('api')->group(function () {

    // ── CU01: Auth (rutas públicas) ────────────────────────────
    Route::post('/login',  [AuthController::class, 'login'])->middleware('throttle:login-attempts');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Rutas protegidas (requieren sesión activa) ─────────────
    Route::middleware(['auth.session', 'throttle:api-general', 'throttle:api-escritura'])->group(function () {

        // ── Admin + Recepcionista + Mecánico (GET compartidos) ───────
        Route::middleware('role:Administrador,Recepcionista,Mecanico')->group(function () {
            Route::get('ordenes-trabajo',        [OrdenTrabajoController::class, 'index']);
            Route::get('ordenes-trabajo/{id}',   [OrdenTrabajoController::class, 'show'])->whereNumber('id');
            Route::patch('ordenes-trabajo/{id}/estado', [OrdenTrabajoController::class, 'updateEstado']);
            Route::get('clientes',               [ClienteController::class, 'index']);
            Route::get('clientes/{id}',          [ClienteController::class, 'show'])->whereNumber('id');
            Route::get('citas',                  [CitaController::class, 'index']);
            Route::get('citas/{id}',             [CitaController::class, 'show'])->whereNumber('id');
            Route::get('repuestos',              [RepuestoController::class, 'index']);
            Route::get('inventario',             [InventarioController::class, 'index']);
            Route::get('sucursales',             fn() => response()->json(\Illuminate\Support\Facades\Cache::remember('cat_sucursales', 3600, fn() => \App\Models\Sucursal::all())));
            Route::get('especialidades',         fn() => response()->json(\Illuminate\Support\Facades\Cache::remember('cat_especialidades', 3600, fn() => \App\Models\Especialidad::all())));
            Route::get('mecanicos',              [MecanicoController::class, 'index']);
        });

        // ── Admin + Recepcionista (escrituras y rutas exclusivas) ────
        Route::middleware('role:Administrador,Recepcionista')->group(function () {
            Route::post('clientes',             [ClienteController::class, 'store']);
            Route::put('clientes/{id}',         [ClienteController::class, 'update'])->whereNumber('id');
            Route::patch('clientes/{id}',       [ClienteController::class, 'update'])->whereNumber('id');
            Route::post('clientes/con-usuario', [ClienteController::class, 'storeConUsuario']);
            Route::get('clientes/buscar',       [ClienteController::class, 'buscar']);
            
            Route::post('citas',                [CitaController::class, 'store']);
            Route::put('citas/{id}',            [CitaController::class, 'update']);
            Route::patch('citas/{id}',          [CitaController::class, 'update']);
            Route::get('citas/hoy',             [CitaController::class, 'hoy']);
            
            Route::apiResource('vehiculos',     VehiculoController::class);
            Route::get('/modelos-vehiculo',     fn() => response()->json(\Illuminate\Support\Facades\Cache::remember('cat_modelos_vehiculo', 3600, fn() => \App\Models\ModeloVehiculo::with('marca')->get())));
            Route::apiResource('tipos-servicio', TipoServicioController::class);
            Route::apiResource('servicios',     ServicioController::class);
            Route::apiResource('proveedores',   ProveedorController::class);
            Route::apiResource('pagos',         PagoController::class)->only(['index','store','show','update']);
            Route::apiResource('facturas',      FacturaController::class)->only(['index','store','show','update']);
            Route::get('mecanicos/con-carga',   [MecanicoController::class, 'conCarga']);
            Route::get('notificaciones/no-leidas', [NotificacionController::class, 'noLeidas']);
            Route::apiResource('notificaciones', NotificacionController::class)->only(['index','store','show','update']);
            
            Route::post('ordenes-trabajo',      [OrdenTrabajoController::class, 'store']);
            Route::put('ordenes-trabajo/{id}',  [OrdenTrabajoController::class, 'update']);
        });

        // ── Admin + Mecánico (acciones operativas del mecánico) ──────
        Route::middleware('role:Administrador,Mecanico')->group(function () {
            Route::post('ordenes-trabajo/{id}/aceptar', [OrdenTrabajoController::class, 'aceptar']);
            Route::patch('mecanicos/mi-disponibilidad', [MecanicoController::class, 'toggleDisponible']);
            Route::apiResource('diagnosticos',   DiagnosticoController::class);
            Route::apiResource('detalles-orden', DetalleOrdenTrabajoController::class);
            Route::post('movimientos-inventario', [MovimientoInventarioController::class, 'store']);
        });

        // ── Solo Administrador ──────────────────────────────────────
        Route::middleware('role:Administrador')->group(function () {
            Route::apiResource('usuarios',  UsuarioController::class);
            Route::apiResource('roles',     RolController::class);
            Route::get('roles/{id}/permisos', [RolController::class, 'getRolPermisos']);
            Route::apiResource('permisos',  PermisoController::class);
            Route::get('reportes/{id}/data',     [ReporteController::class, 'data']);
            Route::get('reportes/{id}/download', [ReporteController::class, 'download'])->middleware('throttle:descargas');
            Route::apiResource('reportes',  ReporteController::class);
            Route::apiResource('mecanicos', MecanicoController::class)->only(['store','update','destroy']);
            
            // DELETE exclusivos de admin:
            Route::delete('clientes/{id}',        [ClienteController::class, 'destroy']);
            Route::delete('citas/{id}',           [CitaController::class, 'destroy']);
            Route::delete('ordenes-trabajo/{id}', [OrdenTrabajoController::class, 'destroy']);
            Route::delete('pagos/{id}',           [PagoController::class, 'destroy']);
            Route::delete('facturas/{id}',        [FacturaController::class, 'destroy']);
            Route::delete('repuestos/{id}',       [RepuestoController::class, 'destroy']);
            Route::delete('inventario/{id}',      [InventarioController::class, 'destroy']);
        });

        // ── Solo Cliente (portal propio) ─────────────────────────────
        Route::middleware('role:Cliente')->prefix('client')->group(function () {
            Route::get('/profile',           [\App\Http\Controllers\ClientePortalController::class, 'getProfile']);
            Route::put('/profile',           [\App\Http\Controllers\ClientePortalController::class, 'updateProfile']);
            Route::get('/estado-vehiculo',   [\App\Http\Controllers\ClientePortalController::class, 'getEstadoVehiculo']);
            Route::get('/notificaciones',    [\App\Http\Controllers\ClientePortalController::class, 'getNotificacionesCliente']);
            Route::get('/historial',         [\App\Http\Controllers\ClientePortalController::class, 'getHistorial']);
            // Endpoints legacy (mantener por compatibilidad):
            Route::get('/catalogo',          [\App\Http\Controllers\ClientePortalController::class, 'getCatalogo']);
            Route::get('/solicitudes',       [\App\Http\Controllers\ClientePortalController::class, 'getSolicitudes']);
            Route::post('/solicitudes',      [\App\Http\Controllers\ClientePortalController::class, 'createSolicitud']);
        });

    });
});


