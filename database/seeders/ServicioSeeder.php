<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        // idTipoServicio: 1=Mantenimiento preventivo, 2=Reparación mecánica,
        // 3=Electricidad automotriz, 4=Carrocería y pintura,
        // 5=Diagnóstico computarizado, 6=Sistema de frenos,
        // 7=Aire acondicionado, 8=Suspensión y dirección
        $servicios = [
            // Mantenimiento preventivo
            ['idTipoServicio' => 1, 'nombre' => 'Cambio de aceite y filtro',              'precioBase' => 150.00, 'duracionEstimada' => 30],
            ['idTipoServicio' => 1, 'nombre' => 'Cambio de filtro de aire',               'precioBase' =>  80.00, 'duracionEstimada' => 20],
            ['idTipoServicio' => 1, 'nombre' => 'Cambio de filtro de combustible',        'precioBase' =>  90.00, 'duracionEstimada' => 25],
            ['idTipoServicio' => 1, 'nombre' => 'Limpieza de inyectores',                 'precioBase' => 200.00, 'duracionEstimada' => 60],
            ['idTipoServicio' => 1, 'nombre' => 'Revisión y regulación de válvulas',      'precioBase' => 350.00, 'duracionEstimada' => 120],

            // Reparación mecánica
            ['idTipoServicio' => 2, 'nombre' => 'Reparación de motor (overhaul)',         'precioBase' => 3500.00,'duracionEstimada' => 1440],
            ['idTipoServicio' => 2, 'nombre' => 'Cambio de correa de distribución',       'precioBase' => 450.00, 'duracionEstimada' => 180],
            ['idTipoServicio' => 2, 'nombre' => 'Reparación de caja de cambios',          'precioBase' => 1200.00,'duracionEstimada' => 480],
            ['idTipoServicio' => 2, 'nombre' => 'Cambio de embrague (clutch)',            'precioBase' =>  800.00,'duracionEstimada' => 360],
            ['idTipoServicio' => 2, 'nombre' => 'Reparación de junta de culata',         'precioBase' =>  900.00,'duracionEstimada' => 300],

            // Electricidad automotriz
            ['idTipoServicio' => 3, 'nombre' => 'Diagnóstico eléctrico general',          'precioBase' => 120.00, 'duracionEstimada' => 60],
            ['idTipoServicio' => 3, 'nombre' => 'Cambio de alternador',                   'precioBase' => 450.00, 'duracionEstimada' => 90],
            ['idTipoServicio' => 3, 'nombre' => 'Cambio de motor de arranque',            'precioBase' => 350.00, 'duracionEstimada' => 90],
            ['idTipoServicio' => 3, 'nombre' => 'Instalación de alarma',                  'precioBase' => 250.00, 'duracionEstimada' => 120],

            // Diagnóstico computarizado
            ['idTipoServicio' => 5, 'nombre' => 'Escaneo con scanner OBD-II',            'precioBase' => 100.00, 'duracionEstimada' => 30],
            ['idTipoServicio' => 5, 'nombre' => 'Borrado de códigos de falla',            'precioBase' =>  60.00, 'duracionEstimada' => 15],

            // Sistema de frenos
            ['idTipoServicio' => 6, 'nombre' => 'Cambio de pastillas de freno (delantera)','precioBase' => 180.00,'duracionEstimada' => 60],
            ['idTipoServicio' => 6, 'nombre' => 'Cambio de pastillas de freno (trasera)', 'precioBase' => 180.00, 'duracionEstimada' => 60],
            ['idTipoServicio' => 6, 'nombre' => 'Rectificación de discos de freno',       'precioBase' => 250.00, 'duracionEstimada' => 90],
            ['idTipoServicio' => 6, 'nombre' => 'Cambio de líquido de frenos',            'precioBase' =>  80.00, 'duracionEstimada' => 30],

            // Aire acondicionado
            ['idTipoServicio' => 7, 'nombre' => 'Carga de gas refrigerante R-134a',      'precioBase' => 220.00, 'duracionEstimada' => 45],
            ['idTipoServicio' => 7, 'nombre' => 'Limpieza del sistema de A/C',            'precioBase' => 150.00, 'duracionEstimada' => 60],
            ['idTipoServicio' => 7, 'nombre' => 'Cambio de compresor de A/C',             'precioBase' => 800.00, 'duracionEstimada' => 180],

            // Suspensión y dirección
            ['idTipoServicio' => 8, 'nombre' => 'Cambio de amortiguadores delanteros',   'precioBase' => 350.00, 'duracionEstimada' => 120],
            ['idTipoServicio' => 8, 'nombre' => 'Cambio de amortiguadores traseros',     'precioBase' => 300.00, 'duracionEstimada' => 120],
            ['idTipoServicio' => 8, 'nombre' => 'Alineación y balanceo',                 'precioBase' => 120.00, 'duracionEstimada' => 60],
            ['idTipoServicio' => 8, 'nombre' => 'Cambio de rotulas y terminales',        'precioBase' => 280.00, 'duracionEstimada' => 90],
        ];

        foreach ($servicios as $servicio) {
            DB::table('servicio')->updateOrInsert(
                ['nombre' => $servicio['nombre']],
                $servicio
            );
        }
    }
}
