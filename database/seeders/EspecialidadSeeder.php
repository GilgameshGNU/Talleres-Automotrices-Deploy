<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['nombre' => 'Mecánica general', 'descripcion' => 'Mantenimiento y reparación general del vehículo'],
            ['nombre' => 'Motor', 'descripcion' => 'Diagnóstico y reparación de motor'],
            ['nombre' => 'Frenos', 'descripcion' => 'Sistema de frenos'],
            ['nombre' => 'Electricidad automotriz', 'descripcion' => 'Sistema eléctrico y electrónico'],
            ['nombre' => 'Transmisión', 'descripcion' => 'Caja de cambios y transmisión'],
            ['nombre' => 'Aire acondicionado', 'descripcion' => 'Climatización del vehículo'],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(['nombre' => $especialidad['nombre']], $especialidad);
        }
    }
}
