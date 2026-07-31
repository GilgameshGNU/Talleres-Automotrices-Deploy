<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Administrador (idRol = 1)
            ['idRol' => 1, 'nombre' => 'Ver usuarios',         'modulo' => 'Usuarios'],
            ['idRol' => 1, 'nombre' => 'Crear usuarios',       'modulo' => 'Usuarios'],
            ['idRol' => 1, 'nombre' => 'Editar usuarios',      'modulo' => 'Usuarios'],
            ['idRol' => 1, 'nombre' => 'Eliminar usuarios',    'modulo' => 'Usuarios'],
            ['idRol' => 1, 'nombre' => 'Ver reportes',         'modulo' => 'Reportes'],
            ['idRol' => 1, 'nombre' => 'Exportar reportes',    'modulo' => 'Reportes'],
            ['idRol' => 1, 'nombre' => 'Gestionar inventario', 'modulo' => 'Inventario'],
            ['idRol' => 1, 'nombre' => 'Ver auditoría',        'modulo' => 'Auditoria'],

            // Recepcionista (idRol = 2)
            ['idRol' => 2, 'nombre' => 'Ver clientes',         'modulo' => 'Clientes'],
            ['idRol' => 2, 'nombre' => 'Crear clientes',       'modulo' => 'Clientes'],
            ['idRol' => 2, 'nombre' => 'Editar clientes',      'modulo' => 'Clientes'],
            ['idRol' => 2, 'nombre' => 'Ver citas',            'modulo' => 'Citas'],
            ['idRol' => 2, 'nombre' => 'Crear citas',          'modulo' => 'Citas'],
            ['idRol' => 2, 'nombre' => 'Cancelar citas',       'modulo' => 'Citas'],
            ['idRol' => 2, 'nombre' => 'Ver órdenes trabajo',  'modulo' => 'OrdenTrabajo'],
            ['idRol' => 2, 'nombre' => 'Crear facturas',       'modulo' => 'Facturacion'],

            // Mecánico (idRol = 3)
            ['idRol' => 3, 'nombre' => 'Ver órdenes asignadas','modulo' => 'OrdenTrabajo'],
            ['idRol' => 3, 'nombre' => 'Actualizar orden',     'modulo' => 'OrdenTrabajo'],
            ['idRol' => 3, 'nombre' => 'Registrar diagnóstico','modulo' => 'Diagnostico'],
            ['idRol' => 3, 'nombre' => 'Ver inventario',       'modulo' => 'Inventario'],

            // Cliente (idRol = 4)
            ['idRol' => 4, 'nombre' => 'Ver mis citas',        'modulo' => 'Citas'],
            ['idRol' => 4, 'nombre' => 'Crear cita propia',    'modulo' => 'Citas'],
            ['idRol' => 4, 'nombre' => 'Ver mis vehículos',    'modulo' => 'Vehiculos'],
            ['idRol' => 4, 'nombre' => 'Ver mis facturas',     'modulo' => 'Facturacion'],
        ];

        DB::table('permiso')->insert($permisos);
    }
}
