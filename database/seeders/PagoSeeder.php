<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        // idFactura del 1 al 9 (según FacturaSeeder)
        // idMetodoPago: 1=Efectivo, 2=Tarjeta débito, 3=Tarjeta crédito, 4=Transferencia, 5=QR, 6=Cheque
        $pagos = [
            ['idFactura' => 1, 'idMetodoPago' => 5, 'monto' => 380.00, 'fecha' => '2026-06-03 15:30:00', 'estado' => 'Completado'],
            ['idFactura' => 2, 'idMetodoPago' => 1, 'monto' => 430.00, 'fecha' => '2026-06-11 10:15:00', 'estado' => 'Completado'],
            ['idFactura' => 3, 'idMetodoPago' => 4, 'monto' => 220.00, 'fecha' => '2026-06-15 14:00:00', 'estado' => 'Completado'],
            // Pago de factura 4 dividido en dos partes
            ['idFactura' => 4, 'idMetodoPago' => 4, 'monto' => 600.00, 'fecha' => '2026-06-20 09:00:00', 'estado' => 'Completado'],
            ['idFactura' => 4, 'idMetodoPago' => 1, 'monto' => 680.00, 'fecha' => '2026-06-22 17:30:00', 'estado' => 'Completado'],
            
            ['idFactura' => 5, 'idMetodoPago' => 5, 'monto' => 230.00, 'fecha' => '2026-07-01 11:20:00', 'estado' => 'Completado'],
            ['idFactura' => 6, 'idMetodoPago' => 3, 'monto' => 1150.00,'fecha' => '2026-07-08 16:45:00', 'estado' => 'Completado'],
            ['idFactura' => 7, 'idMetodoPago' => 5, 'monto' => 710.00, 'fecha' => '2026-07-11 09:30:00', 'estado' => 'Completado'],
            ['idFactura' => 8, 'idMetodoPago' => 1, 'monto' => 370.00, 'fecha' => '2026-07-15 12:10:00', 'estado' => 'Completado'],
            ['idFactura' => 9, 'idMetodoPago' => 2, 'monto' => 730.00, 'fecha' => '2026-07-21 15:50:00', 'estado' => 'Completado'],
        ];

        foreach ($pagos as $pago) {
            DB::table('pago')->insert($pago);
        }
    }
}
