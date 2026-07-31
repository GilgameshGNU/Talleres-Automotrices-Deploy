<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        $facturas = [
            ['idOrden' => 1, 'nroFactura' => 'F-001001', 'fechaEmision' => '2026-06-03', 'montoTotal' => 380.00, 'nitCliente' => '7654321'],
            ['idOrden' => 2, 'nroFactura' => 'F-001002', 'fechaEmision' => '2026-06-11', 'montoTotal' => 430.00, 'nitCliente' => '5432198'],
            ['idOrden' => 3, 'nroFactura' => 'F-001003', 'fechaEmision' => '2026-06-15', 'montoTotal' => 220.00, 'nitCliente' => '3219876'],
            ['idOrden' => 4, 'nroFactura' => 'F-001004', 'fechaEmision' => '2026-06-22', 'montoTotal' => 1280.00,'nitCliente' => '9871234'],
            ['idOrden' => 5, 'nroFactura' => 'F-001005', 'fechaEmision' => '2026-07-01', 'montoTotal' => 230.00, 'nitCliente' => '6543210'],
            ['idOrden' => 6, 'nroFactura' => 'F-001006', 'fechaEmision' => '2026-07-08', 'montoTotal' => 1150.00,'nitCliente' => '8765432'],
            ['idOrden' => 7, 'nroFactura' => 'F-001007', 'fechaEmision' => '2026-07-11', 'montoTotal' => 710.00, 'nitCliente' => '4321987'],
            ['idOrden' => 8, 'nroFactura' => 'F-001008', 'fechaEmision' => '2026-07-15', 'montoTotal' => 370.00, 'nitCliente' => '2109876'],
            ['idOrden' => 9, 'nroFactura' => 'F-001009', 'fechaEmision' => '2026-07-21', 'montoTotal' => 730.00, 'nitCliente' => '5678901'],
        ];

        foreach ($facturas as $factura) {
            DB::table('factura')->updateOrInsert(
                ['nroFactura' => $factura['nroFactura']],
                $factura
            );
        }
    }
}
