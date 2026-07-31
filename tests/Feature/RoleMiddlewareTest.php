<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_accepts_accented_role_names(): void
    {
        $rol = Rol::create([
            'idRol' => 3,
            'nombre' => 'Mecánico',
            'descripcion' => 'Operaciones de taller',
        ]);

        $usuario = Usuario::create([
            'idRol' => $rol->idRol,
            'nombreUsuario' => 'mecanico_test',
            'email' => 'mecanico@test.com',
            'contrasena' => bcrypt('secret123'),
            'estado' => 1,
        ]);

        Route::middleware('web')->get('/test-mecanico', function () {
            return 'ok';
        })->middleware('role:Mecanico');

        Auth::login($usuario);

        $response = $this->get('/test-mecanico');

        $response->assertStatus(200);
        $response->assertSee('ok');
    }
}
