<?php
// Script temporal para crear usuario cliente de prueba
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

// 1. Crear/buscar el rol Cliente
$rolCliente = Rol::where('nombre', 'Cliente')->first();
if (!$rolCliente) {
    $rolCliente = Rol::create(['nombre' => 'Cliente', 'descripcion' => 'Acceso al portal de cliente']);
}

// Ensure 'admin' user password
$adminUser = Usuario::where('nombreUsuario', 'admin')->first();
if ($adminUser) {
    $adminUser->contrasena = Hash::make('Admin@2024');
    $adminUser->save();
}

// Ensure 'encargado' user password
$encargadoUser = Usuario::where('nombreUsuario', 'encargado')->first();
if ($encargadoUser) {
    $encargadoUser->contrasena = Hash::make('Encargado@2024');
    $encargadoUser->save();
}

// 2. Crear/actualizar usuario 'cliente1' y 'cliente'
$usersData = [
    ['un' => 'cliente1', 'email' => 'cliente@correo.com'],
    ['un' => 'cliente',   'email' => 'cliente2@correo.com']
];
foreach ($usersData as $ud) {
    $un = $ud['un'];
    $email = $ud['email'];
    $u = Usuario::where('nombreUsuario', $un)->first();
    if (!$u) {
        $u = Usuario::create([
            'idRol'         => $rolCliente->idRol,
            'nombreUsuario' => $un,
            'email'         => $email,
            'contrasena'    => Hash::make('Cliente123'),
            'estado'        => 1,
        ]);
        echo "Usuario '$un' creado.\n";
    } else {
        $u->idRol = $rolCliente->idRol;
        $u->email = $email;
        $u->contrasena = Hash::make('Cliente123');
        $u->save();
        echo "Usuario '$un' actualizado.\n";
    }

    // Link to cliente table
    $c = Cliente::where('idUsuario', $u->idUsuario)->first();
    if (!$c) {
        Cliente::create([
            'idUsuario'      => $u->idUsuario,
            'nombreCompleto' => 'Juan Pérez (' . ucfirst($un) . ')',
            'ci_nit'         => '1234567',
            'telefono'       => '70000000',
            'direccion'      => 'Av. Principal #123'
        ]);
        echo "Cliente demo enlazado para '$un'.\n";
    }
}

echo "\n=== LISTO ===\n";
echo "Cuentas habilitadas:\n";
echo "  - Cliente:   'cliente' o 'cliente1' / 'Cliente123'\n";
echo "  - Email:     'cliente@correo.com' o 'cliente1@correo.com' / 'Cliente123'\n";
echo "  - Admin:     'admin' / 'Admin@2024'\n";
echo "  - Encargado: 'encargado' / 'Encargado@2024'\n";
