<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Uso en rutas: ->middleware('role:Administrador,Recepcionista')
     * Acepta uno o más roles separados por coma.
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Cargar relación si no está cargada
        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        $rolNombre = $user->rol?->nombre;
        $normalizedUserRole = $this->normalizeRole($rolNombre);
        $normalizedRequiredRoles = array_map(fn ($role) => $this->normalizeRole($role), $roles);

        if (!in_array($normalizedUserRole, $normalizedRequiredRoles, true)) {
            return response()->json([
                'message' => 'Acceso denegado. Tu rol no tiene permiso para esta operación.',
                'rol_actual' => $rolNombre,
                'roles_requeridos' => $roles,
            ], 403);
        }

        return $next($request);
    }

    private function normalizeRole(?string $role): string
    {
        if ($role === null) {
            return '';
        }

        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $role) ?: $role;

        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $ascii));
    }
}
