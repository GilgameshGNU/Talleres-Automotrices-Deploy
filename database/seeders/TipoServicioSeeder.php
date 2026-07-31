<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoServicioSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Mantenimiento preventivo', 'descripcion' => 'Servicios de mantenimiento programado para prevenir fallas'],
            ['nombre' => 'Reparación mecánica',       'descripcion' => 'Reparación de componentes mecánicos del vehículo'],
            ['nombre' => 'Electricidad automotriz',   'descripcion' => 'Diagnóstico y reparación del sistema eléctrico'],
            ['nombre' => 'Carrocería y pintura',      'descripcion' => 'Corrección de abolladuras, pintura y acabados'],
            ['nombre' => 'Diagnóstico computarizado', 'descripcion' => 'Lectura de códigos de falla mediante scanner'],
            ['nombre' => 'Sistema de frenos',         'descripcion' => 'Revisión, ajuste y cambio de componentes de frenos'],
            ['nombre' => 'Aire acondicionado',        'descripcion' => 'Carga de gas, limpieza y reparación del A/C'],
            ['nombre' => 'Suspensión y dirección',    'descripcion' => 'Revisión y reemplazo de amortiguadores, rotulas y rótulas'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tiposervicio')->updateOrInsert(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
