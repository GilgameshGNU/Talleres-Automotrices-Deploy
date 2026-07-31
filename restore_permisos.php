<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\Rol::where('nombre', 'Administrador')->first();
if ($admin) {
    \App\Models\Permiso::firstOrCreate(['idRol' => $admin->idRol, 'nombre' => 'Gestionar Usuarios', 'modulo' => 'Usuarios & Roles']);
    \App\Models\Permiso::firstOrCreate(['idRol' => $admin->idRol, 'nombre' => 'Gestionar Catálogo', 'modulo' => 'Servicios']);
    \App\Models\Permiso::firstOrCreate(['idRol' => $admin->idRol, 'nombre' => 'Configuración de Sistema', 'modulo' => 'Autenticación']);
    echo "Permisos de Administrador restaurados.\n";
} else {
    echo "Rol Administrador no encontrado.\n";
}

$encargado = \App\Models\Rol::where('nombre', 'Encargado')->first();
if ($encargado) {
    \App\Models\Permiso::firstOrCreate(['idRol' => $encargado->idRol, 'nombre' => 'Ver Usuarios', 'modulo' => 'Usuarios & Roles']);
    \App\Models\Permiso::firstOrCreate(['idRol' => $encargado->idRol, 'nombre' => 'Gestionar Catálogo', 'modulo' => 'Servicios']);
    echo "Permisos de Encargado restaurados.\n";
}
