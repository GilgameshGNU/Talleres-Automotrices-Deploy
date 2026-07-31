<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo',              'comision' => 0.00],
            ['nombre' => 'Tarjeta de débito',     'comision' => 0.50],
            ['nombre' => 'Tarjeta de crédito',    'comision' => 2.50],
            ['nombre' => 'Transferencia bancaria','comision' => 0.00],
            ['nombre' => 'QR (Banca Móvil)',      'comision' => 0.00],
            ['nombre' => 'Cheque',                'comision' => 1.00],
        ];

        foreach ($metodos as $metodo) {
            DB::table('metodopago')->updateOrInsert(['nombre' => $metodo['nombre']], $metodo);
        }
    }
}
