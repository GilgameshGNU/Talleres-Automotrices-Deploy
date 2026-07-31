<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenTrabajoSeeder extends Seeder
{
    public function run(): void
    {
        // idCliente 1-10, idVehiculo 1-11, idMecanico 1-6
        $ordenes = [
            [
                'idCliente'     => 1,
                'idVehiculo'    => 1,
                'idMecanico'    => 1,
                'fechaIngreso'  => '2026-06-02',
                'fechaEntrega'  => '2026-06-03',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Mantenimiento preventivo de 40 000 km. Se realizó cambio de aceite 5W-30, filtros y revisión general.',
                'total'         => 380.00,
            ],
            [
                'idCliente'     => 2,
                'idVehiculo'    => 3,
                'idMecanico'    => 3,
                'fechaIngreso'  => '2026-06-10',
                'fechaEntrega'  => '2026-06-11',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Cliente reportó ruidos al frenar. Se cambió pastillas de freno delanteras y se rectificó discos.',
                'total'         => 430.00,
            ],
            [
                'idCliente'     => 3,
                'idVehiculo'    => 4,
                'idMecanico'    => 4,
                'fechaIngreso'  => '2026-06-15',
                'fechaEntrega'  => '2026-06-15',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Diagnóstico eléctrico general y escaneo OBD-II. Se detectaron 2 códigos de falla de sensor de oxígeno.',
                'total'         => 220.00,
            ],
            [
                'idCliente'     => 4,
                'idVehiculo'    => 5,
                'idMecanico'    => 2,
                'fechaIngreso'  => '2026-06-20',
                'fechaEntrega'  => '2026-06-22',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Cambio de embrague (clutch) completo. Kit LuK instalado.',
                'total'         => 1280.00,
            ],
            [
                'idCliente'     => 5,
                'idVehiculo'    => 6,
                'idMecanico'    => 1,
                'fechaIngreso'  => '2026-07-01',
                'fechaEntrega'  => '2026-07-01',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Cambio de aceite y filtros a los 12 000 km de garantía.',
                'total'         => 230.00,
            ],
            [
                'idCliente'     => 6,
                'idVehiculo'    => 7,
                'idMecanico'    => 2,
                'fechaIngreso'  => '2026-07-05',
                'fechaEntrega'  => '2026-07-08',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Motor con pérdida de compresión en cilindro 2. Se realizó reparación de junta de culata.',
                'total'         => 1150.00,
            ],
            [
                'idCliente'     => 7,
                'idVehiculo'    => 8,
                'idMecanico'    => 5,
                'fechaIngreso'  => '2026-07-10',
                'fechaEntrega'  => '2026-07-11',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Suspensión delantera deteriorada. Cambio de amortiguadores delanteros y rótulas.',
                'total'         => 710.00,
            ],
            [
                'idCliente'     => 8,
                'idVehiculo'    => 9,
                'idMecanico'    => 4,
                'fechaIngreso'  => '2026-07-15',
                'fechaEntrega'  => '2026-07-15',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Carga de gas refrigerante R-134a y limpieza del sistema de aire acondicionado.',
                'total'         => 370.00,
            ],
            [
                'idCliente'     => 9,
                'idVehiculo'    => 10,
                'idMecanico'    => 1,
                'fechaIngreso'  => '2026-07-20',
                'fechaEntrega'  => '2026-07-21',
                'estado'        => 'Entregado',
                'diagnostico'   => 'Cambio de correa de distribución y revisión de tensores.',
                'total'         => 730.00,
            ],
            [
                'idCliente'     => 10,
                'idVehiculo'    => 11,
                'idMecanico'    => 3,
                'fechaIngreso'  => '2026-07-22',
                'fechaEntrega'  => null,
                'estado'        => 'En proceso',
                'diagnostico'   => 'Vehículo recibido con falla de arranque. Pendiente diagnóstico eléctrico detallado.',
                'total'         => 0.00,
            ],
            [
                'idCliente'     => 1,
                'idVehiculo'    => 2,
                'idMecanico'    => null,
                'fechaIngreso'  => '2026-07-26',
                'fechaEntrega'  => null,
                'estado'        => 'Pendiente',
                'diagnostico'   => null,
                'total'         => 0.00,
            ],
            [
                'idCliente'     => 2,
                'idVehiculo'    => 3,
                'idMecanico'    => 3,
                'fechaIngreso'  => '2026-07-27',
                'fechaEntrega'  => null,
                'estado'        => 'En proceso',
                'diagnostico'   => 'Revisión de sistema de frenos trasero. Cliente refiere que el pedal baja demasiado.',
                'total'         => 0.00,
            ],
        ];

        foreach ($ordenes as $orden) {
            DB::table('ordentrabajo')->insert($orden);
        }
    }
}
