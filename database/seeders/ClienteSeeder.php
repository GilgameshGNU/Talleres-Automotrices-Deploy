<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Los idUsuario 12..16 corresponden a los clientes creados en UsuarioSeeder
        // (indices 1-based: admin=1, erivaldo=2, moises=3, sequeiro=4,
        //  recepc02=5, recepcion1=6, quiroga=7, mamani=8, condori=9, quispe=10, mecanico=11,
        //  cli_quispe=12, cli_flores=13, cli_mamani=14, cli_garcia=15, cli_romero=16)
        $clientes = [
            [
                'idUsuario'      => 9,
                'nombreCompleto' => 'Mario Quispe Chura',
                'ci_nit'         => '7654321',
                'telefono'       => '72345678',   // número boliviano móvil real (Tigo)
                'direccion'      => 'Av. Montes 456, La Paz',
            ],
            [
                'idUsuario'      => 10,
                'nombreCompleto' => 'Ana Flores Vargas',
                'ci_nit'         => '5432198',
                'telefono'       => '68901234',   // número boliviano móvil (Entel)
                'direccion'      => 'Calle Potosí 234, Cochabamba',
            ],
            [
                'idUsuario'      => 11,
                'nombreCompleto' => 'Pedro Mamani Ticona',
                'ci_nit'         => '3219876',
                'telefono'       => '76543210',   // número boliviano móvil (Tigo)
                'direccion'      => 'Av. Blanco Galindo Km 5, Cochabamba',
            ],
            [
                'idUsuario'      => 12,
                'nombreCompleto' => 'Lucía García Montaño',
                'ci_nit'         => '9871234',
                'telefono'       => '61234567',   // número boliviano móvil (Entel)
                'direccion'      => 'Calle España 789, Santa Cruz',
            ],
            [
                'idUsuario'      => 13,
                'nombreCompleto' => 'Carlos Romero Salazar',
                'ci_nit'         => '6543210',
                'telefono'       => '77891234',   // número boliviano móvil (Tigo)
                'direccion'      => 'Av. Busch 101, La Paz',
            ],
            // Clientes sin cuenta de usuario
            [
                'idUsuario'      => null,
                'nombreCompleto' => 'Rosa Condori Layme',
                'ci_nit'         => '8765432',
                'telefono'       => '67890123',   // Entel
                'direccion'      => 'Av. 6 de Agosto 332, La Paz',
            ],
            [
                'idUsuario'      => null,
                'nombreCompleto' => 'Jorge Aliaga Ponce',
                'ci_nit'         => '4321987',
                'telefono'       => '73456789',   // Tigo
                'direccion'      => 'Calle Junín 55, Oruro',
            ],
            [
                'idUsuario'      => null,
                'nombreCompleto' => 'Beatriz Zenteno Cruz',
                'ci_nit'         => '2109876',
                'telefono'       => '69012345',   // Entel
                'direccion'      => 'Av. Petrolera Km 3, Santa Cruz',
            ],
            [
                'idUsuario'      => null,
                'nombreCompleto' => 'Raúl Chávez Torrico',
                'ci_nit'         => '5678901',
                'telefono'       => '71234567',   // Tigo
                'direccion'      => 'Calle Sucre 88, Tarija',
            ],
            [
                'idUsuario'      => null,
                'nombreCompleto' => 'Sandra Vega Oporto',
                'ci_nit'         => '3456789',
                'telefono'       => '62345678',   // Entel
                'direccion'      => 'Av. Hernando Siles 200, La Paz',
            ],
        ];

        foreach ($clientes as $cliente) {
            DB::table('cliente')->updateOrInsert(
                ['ci_nit' => $cliente['ci_nit']],
                $cliente
            );
        }
    }
}
