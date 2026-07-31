<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

$user = Usuario::where('email', 'cliente@correo.com')->first();
echo "User found: " . ($user ? 'Yes' : 'No') . PHP_EOL;

if ($user) {
    echo "Hash in DB: " . $user->contrasena . PHP_EOL;
    $credentials = ['email' => 'cliente@correo.com', 'password' => 'Cliente123'];
    echo "Auth attempt with 'password' key: " . (Auth::attempt($credentials) ? 'Success' : 'Failed') . PHP_EOL;
    
    $credentials2 = ['email' => 'cliente@correo.com', 'contrasena' => 'Cliente123'];
    echo "Auth attempt with 'contrasena' key: " . (Auth::attempt($credentials2) ? 'Success' : 'Failed') . PHP_EOL;
}
