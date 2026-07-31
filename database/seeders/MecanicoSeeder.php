<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MecanicoSeeder extends Seeder
{
    public function run(): void
    {
        // idSucursal: 1 = La Paz, 2 = Cochabamba, 3 = Santa Cruz
        $mecanicos = [
            [
                'nombreCompleto' => 'Luis Fernando Quiroga Arias',
                'ci'             => '8231456',
                'telefono'       => '72178934',   // Tigo Bolivia
                'idSucursal'     => 1,
            ],
            [
                'nombreCompleto' => 'Roberto Mamani Condori',
                'ci'             => '6543201',
                'telefono'       => '68542310',   // Entel Bolivia
                'idSucursal'     => 1,
            ],
            [
                'nombreCompleto' => 'Edwin Condori Ticona',
                'ci'             => '5312874',
                'telefono'       => '76812345',   // Tigo Bolivia
                'idSucursal'     => 2,
            ],
            [
                'nombreCompleto' => 'Gabriel Quispe Flores',
                'ci'             => '7891234',
                'telefono'       => '67234891',   // Entel Bolivia
                'idSucursal'     => 2,
            ],
            [
                'nombreCompleto' => 'Marcos Aliaga Balderrama',
                'ci'             => '4523678',
                'telefono'       => '78901234',   // Tigo Bolivia
                'idSucursal'     => 3,
            ],
            [
                'nombreCompleto' => 'Héctor Vásquez Pereira',
                'ci'             => '3109876',
                'telefono'       => '69345678',   // Entel Bolivia
                'idSucursal'     => 3,
            ],
        ];

        foreach ($mecanicos as $mecanico) {
            DB::table('mecanico')->updateOrInsert(
                ['ci' => $mecanico['ci']],
                $mecanico
            );
        }
    }
}
