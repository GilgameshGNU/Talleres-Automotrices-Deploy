<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['idRol' => 1, 'nombre' => 'Administrador',  'descripcion' => 'Acceso total al sistema'],
            ['idRol' => 2, 'nombre' => 'Recepcionista',  'descripcion' => 'Gestión de clientes, vehículos y citas'],
            ['idRol' => 3, 'nombre' => 'Mecánico',        'descripcion' => 'Ejecución y registro de órdenes de trabajo'],
            ['idRol' => 4, 'nombre' => 'Cliente',         'descripcion' => 'Acceso al portal de clientes'],
        ];

        foreach ($roles as $rol) {
            DB::table('rol')->updateOrInsert(['idRol' => $rol['idRol']], $rol);
        }
    }
}
