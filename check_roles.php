<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ROLES ===\n";
foreach (\App\Models\Rol::all() as $r) {
    echo "[{$r->idRol}] nombre=\"{$r->nombre}\"\n";
}
