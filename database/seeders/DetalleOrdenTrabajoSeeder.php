<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleOrdenTrabajoSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [
            // Orden 1: Mantenimiento preventivo de 40 000 km (Total: 380.00)
            // - Servicio: Cambio de aceite (id 1, 150.00)
            // - Repuesto: Aceite Mobil 5W-30 (id 1, 45.00 x 4 = 180.00)
            // - Repuesto: Filtro de aceite Toyota (id 5, 35.00 x 1 = 35.00)
            // - Servicio: Borrado códigos (id 15, 15.00 - ajustado a 15) -> Total: 150+180+35+15 = 380.00
            ['idOrden' => 1, 'idServicio' => 1, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 150.00, 'subtotal' => 150.00, 'observaciones' => 'Cambio de aceite de motor.'],
            ['idOrden' => 1, 'idServicio' => null, 'idRepuesto' => 1, 'cantidad' => 4, 'precioUnitario' =>  45.00, 'subtotal' => 180.00, 'observaciones' => 'Aceite Mobil 1 5W-30.'],
            ['idOrden' => 1, 'idServicio' => null, 'idRepuesto' => 5, 'cantidad' => 1, 'precioUnitario' =>  35.00, 'subtotal' =>  35.00, 'observaciones' => 'Filtro de aceite original Toyota.'],
            ['idOrden' => 1, 'idServicio' => 16, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 15.00, 'subtotal' =>  15.00, 'observaciones' => 'Reseteo de luz de mantenimiento.'],

            // Orden 2: Ruidos al frenar (Total: 430.00)
            // - Servicio: Cambio pastillas del (id 17, 180.00)
            // - Servicio: Rectificación discos (id 19, 250.00) -> Total 430.00
            ['idOrden' => 2, 'idServicio' => 17, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 180.00, 'subtotal' => 180.00, 'observaciones' => 'Mano de obra cambio pastillas delanteras.'],
            ['idOrden' => 2, 'idServicio' => 19, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 250.00, 'subtotal' => 250.00, 'observaciones' => 'Rectificación de discos delanteros.'],

            // Orden 3: Diagnóstico eléctrico (Total: 220.00)
            // - Servicio: Diagnóstico eléctrico (id 11, 120.00)
            // - Servicio: Escaneo OBD-II (id 15, 100.00) -> Total 220.00
            ['idOrden' => 3, 'idServicio' => 11, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 120.00, 'subtotal' => 120.00, 'observaciones' => 'Revisión cableado sensor de oxígeno.'],
            ['idOrden' => 3, 'idServicio' => 15, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 100.00, 'subtotal' => 100.00, 'observaciones' => 'Lectura y confirmación de códigos.'],

            // Orden 4: Cambio de embrague (Total: 1280.00)
            // - Servicio: Cambio de embrague (id 9, 800.00)
            // - Repuesto: Kit de embrague Chevrolet (id 23, 480.00) -> Total 1280.00
            ['idOrden' => 4, 'idServicio' => 9, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 800.00, 'subtotal' => 800.00, 'observaciones' => 'Bajada de caja y reemplazo de kit completo.'],
            ['idOrden' => 4, 'idServicio' => null, 'idRepuesto' => 23, 'cantidad' => 1, 'precioUnitario' => 480.00, 'subtotal' => 480.00, 'observaciones' => 'Kit de embrague LuK nuevo.'],

            // Orden 5: Cambio de aceite 12 000 km (Total: 230.00)
            // - Servicio: Cambio de aceite (id 1, 150.00)
            // - Repuesto: Filtro de aceite Toyota (id 5, 35.00)
            // - Repuesto: Aceite Mobil 5W-30 (id 1, 45.00 x 1) -> Total 230.00
            ['idOrden' => 5, 'idServicio' => 1, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 150.00, 'subtotal' => 150.00, 'observaciones' => 'Servicio básico de mantenimiento.'],
            ['idOrden' => 5, 'idServicio' => null, 'idRepuesto' => 5, 'cantidad' => 1, 'precioUnitario' =>  35.00, 'subtotal' =>  35.00, 'observaciones' => 'Filtro original.'],
            ['idOrden' => 5, 'idServicio' => null, 'idRepuesto' => 1, 'cantidad' => 1, 'precioUnitario' =>  45.00, 'subtotal' =>  45.00, 'observaciones' => 'Relleno de nivel de aceite.'],

            // Orden 6: Reparación de junta de culata (Total: 1150.00)
            // - Servicio: Reparación junta de culata (id 10, 900.00)
            // - Repuesto: Kit distribución VW (id 14, 250.00) -> Total 1150.00
            ['idOrden' => 6, 'idServicio' => 10, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 900.00, 'subtotal' => 900.00, 'observaciones' => 'Desmontaje de culata y limpieza de motor.'],
            ['idOrden' => 6, 'idServicio' => null, 'idRepuesto' => 14, 'cantidad' => 1, 'precioUnitario' => 250.00, 'subtotal' => 250.00, 'observaciones' => 'Se aprovechó para cambiar distribución.'],

            // Orden 7: Suspensión delantera (Total: 710.00)
            // - Servicio: Cambio amortiguadores delanteros (id 24, 350.00)
            // - Repuesto: Amortiguador delantero Toyota (id 16, 220.00)
            // - Servicio: Alineación (id 26, 140.00) -> Total 710.00
            ['idOrden' => 7, 'idServicio' => 24, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 350.00, 'subtotal' => 350.00, 'observaciones' => 'Reemplazo de ambos amortiguadores.'],
            ['idOrden' => 7, 'idServicio' => null, 'idRepuesto' => 16, 'cantidad' => 1, 'precioUnitario' => 220.00, 'subtotal' => 220.00, 'observaciones' => 'Amortiguador Monroe.'],
            ['idOrden' => 7, 'idServicio' => 26, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 140.00, 'subtotal' => 140.00, 'observaciones' => 'Alineación y balanceo tras trabajo en suspensión.'],

            // Orden 8: Aire Acondicionado (Total: 370.00)
            // - Servicio: Carga gas R-134a (id 21, 220.00)
            // - Servicio: Limpieza A/C (id 22, 150.00) -> Total 370.00
            ['idOrden' => 8, 'idServicio' => 21, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 220.00, 'subtotal' => 220.00, 'observaciones' => 'Recarga completa del sistema.'],
            ['idOrden' => 8, 'idServicio' => 22, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 150.00, 'subtotal' => 150.00, 'observaciones' => 'Limpieza de ductos y filtro de cabina.'],

            // Orden 9: Correa de distribución (Total: 730.00)
            // - Servicio: Cambio correa distribución (id 7, 450.00)
            // - Repuesto: Kit correa distribución Toyota (id 13, 280.00) -> Total 730.00
            ['idOrden' => 9, 'idServicio' => 7, 'idRepuesto' => null, 'cantidad' => 1, 'precioUnitario' => 450.00, 'subtotal' => 450.00, 'observaciones' => 'Mano de obra cambio de correa.'],
            ['idOrden' => 9, 'idServicio' => null, 'idRepuesto' => 13, 'cantidad' => 1, 'precioUnitario' => 280.00, 'subtotal' => 280.00, 'observaciones' => 'Kit completo Gates instalado.'],
        ];

        foreach ($detalles as $detalle) {
            DB::table('detalleordentrabajo')->insert($detalle);
        }
    }
}
