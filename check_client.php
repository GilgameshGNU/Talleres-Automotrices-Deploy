<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Verificacion de Login Cliente ===\n\n";

// Check if client role exists
$rolCliente = Rol::where('nombre', 'Cliente')->first();
if ($rolCliente) {
    echo "Rol 'Cliente' encontrado (ID: {$rolCliente->idRol})\n";
} else {
    echo "ERROR: Rol 'Cliente' NO EXISTE\n";
}

// Check if client user exists
$usuario = Usuario::where('email', 'cliente@correo.com')->first();
if ($usuario) {
    echo "Usuario encontrado:\n";
    echo "  - email: {$usuario->email}\n";
    echo "  - nombreUsuario: {$usuario->nombreUsuario}\n";
    echo "  - idRol: {$usuario->idRol}\n";
    echo "  - estado: {$usuario->estado}\n";
    echo "  - contrasena hash: " . substr($usuario->contrasena, 0, 30) . "...\n";
    echo "  - getAuthPasswordName(): " . $usuario->getAuthPasswordName() . "\n";
    
    // Verify password
    $passCheck = Hash::check('Cliente123', $usuario->contrasena);
    echo "  - Verificar 'Cliente123': " . ($passCheck ? 'CORRECTA' : 'INCORRECTA') . "\n";
    
    // Try Auth::attempt
    $attempt1 = Auth::attempt([
        'email' => 'cliente@correo.com',
        'password' => 'Cliente123'
    ]);
    echo "\nAuth::attempt con email+password: " . ($attempt1 ? 'EXITOSO' : 'FALLIDO') . "\n";
    
    Auth::logout();
    
    $attempt2 = Auth::attempt([
        'nombreUsuario' => 'cliente1',
        'password' => 'Cliente123'
    ]);
    echo "Auth::attempt con nombreUsuario+password: " . ($attempt2 ? 'EXITOSO' : 'FALLIDO') . "\n";
    
} else {
    echo "ERROR: Usuario 'cliente@correo.com' NO EXISTE\n";
    echo "Ejecuta: php setup_cliente.php\n";
}

// List all users
echo "\n=== Todos los usuarios ===\n";
$todos = Usuario::with('rol')->get();
foreach ($todos as $u) {
    $rol = $u->rol ? $u->rol->nombre : 'Sin rol';
    echo "  [{$u->idUsuario}] {$u->nombreUsuario} | {$u->email} | Rol: {$rol} | Estado: {$u->estado}\n";
}
