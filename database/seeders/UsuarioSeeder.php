<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de prueba iniciales.
        // Solo estos usuarios semeados tendrán la contraseña fija 123123.
        // No se sobrescriben usuarios existentes.
        $usuarios = [
            ['idRol' => 1, 'nombreUsuario' => 'admin'],
            ['idRol' => 1, 'nombreUsuario' => 'erivaldo'],
            ['idRol' => 1, 'nombreUsuario' => 'moises'],
            ['idRol' => 2, 'nombreUsuario' => 'sequeiro'],
            ['idRol' => 2, 'nombreUsuario' => 'recepc02'],
            ['idRol' => 2, 'nombreUsuario' => 'recepcion1'],
            ['idRol' => 3, 'nombreUsuario' => 'quiroga'],
            ['idRol' => 3, 'nombreUsuario' => 'mamani'],
            ['idRol' => 3, 'nombreUsuario' => 'condori'],
            ['idRol' => 3, 'nombreUsuario' => 'quispe'],
            ['idRol' => 3, 'nombreUsuario' => 'mecanico'],
            ['idRol' => 4, 'nombreUsuario' => 'cli_quispe'],
            ['idRol' => 4, 'nombreUsuario' => 'cli_flores'],
            ['idRol' => 4, 'nombreUsuario' => 'cli_mamani'],
            ['idRol' => 4, 'nombreUsuario' => 'cli_garcia'],
            ['idRol' => 4, 'nombreUsuario' => 'cli_romero'],
            ['idRol' => 4, 'nombreUsuario' => 'cliente1'],
        ];

        foreach ($usuarios as $usuario) {
            $domain = match ($usuario['idRol']) {
                1 => 'admin.com',
                2 => 'recep.com',
                3 => 'mec.com',
                4 => 'cliente.com',
            };

            $usuario['email'] = strtolower($usuario['nombreUsuario']) . '@' . $domain;
            $usuario['contrasena'] = Hash::make('123123');
            $usuario['estado'] = 1;

            $existe = DB::table('usuario')
                ->where('nombreUsuario', $usuario['nombreUsuario'])
                ->exists();

            if (! $existe) {
                DB::table('usuario')->insert($usuario);
            }
        }
    }
}
