<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $movimientos = [
            // Entradas iniciales (idInventario 1 a 15 de Sucursal La Paz)
            ['idInventario' => 1, 'tipo' => 'Entrada', 'cantidad' => 25, 'fecha' => '2026-05-01 08:00:00', 'motivo' => 'Inventario inicial'],
            ['idInventario' => 2, 'tipo' => 'Entrada', 'cantidad' => 15, 'fecha' => '2026-05-01 08:00:00', 'motivo' => 'Inventario inicial'],
            ['idInventario' => 5, 'tipo' => 'Entrada', 'cantidad' => 15, 'fecha' => '2026-05-01 08:00:00', 'motivo' => 'Inventario inicial'],

            // Salidas por uso en órdenes
            // Orden 1 usa 4L aceite Mobil (idRepuesto 1, en La Paz sucursal 1 es idInventario 1)
            // y 1 Filtro Toyota (idRepuesto 5, en La Paz es idInventario 5)
            ['idInventario' => 1, 'tipo' => 'Salida', 'cantidad' => 4, 'fecha' => '2026-06-03 10:00:00', 'motivo' => 'Uso en Orden #1'],
            ['idInventario' => 5, 'tipo' => 'Salida', 'cantidad' => 1, 'fecha' => '2026-06-03 10:05:00', 'motivo' => 'Uso en Orden #1'],
            
            // Orden 5 usa 1L aceite Mobil (idInventario 1) y 1 Filtro Toyota (idInventario 5)
            ['idInventario' => 1, 'tipo' => 'Salida', 'cantidad' => 1, 'fecha' => '2026-07-01 14:00:00', 'motivo' => 'Uso en Orden #5'],
            ['idInventario' => 5, 'tipo' => 'Salida', 'cantidad' => 1, 'fecha' => '2026-07-01 14:05:00', 'motivo' => 'Uso en Orden #5'],

            // Un ajuste por merma
            ['idInventario' => 5, 'tipo' => 'Ajuste', 'cantidad' => -1, 'fecha' => '2026-07-15 18:00:00', 'motivo' => 'Filtro dañado en almacén'],
        ];

        foreach ($movimientos as $mov) {
            DB::table('movimientoinventario')->insert($mov);
        }
    }
}
