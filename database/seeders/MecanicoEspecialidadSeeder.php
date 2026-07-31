<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MecanicoEspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        // idMecanico 1-6, idEspecialidad referencia al EspecialidadSeeder existente:
        // 1 = Mecánica general, 2 = Motor, 3 = Frenos,
        // 4 = Electricidad automotriz, 5 = Transmisión, 6 = Aire acondicionado
        $asignaciones = [
            ['idMecanico' => 1, 'idEspecialidad' => 2], // Luis → Motor
            ['idMecanico' => 1, 'idEspecialidad' => 1], // Luis → Mecánica general
            ['idMecanico' => 2, 'idEspecialidad' => 5], // Roberto → Transmisión
            ['idMecanico' => 2, 'idEspecialidad' => 3], // Roberto → Frenos
            ['idMecanico' => 3, 'idEspecialidad' => 2], // Edwin → Motor
            ['idMecanico' => 3, 'idEspecialidad' => 1], // Edwin → Mecánica general
            ['idMecanico' => 4, 'idEspecialidad' => 4], // Gabriel → Electricidad automotriz
            ['idMecanico' => 4, 'idEspecialidad' => 6], // Gabriel → Aire acondicionado
            ['idMecanico' => 5, 'idEspecialidad' => 3], // Marcos → Frenos
            ['idMecanico' => 5, 'idEspecialidad' => 1], // Marcos → Mecánica general
            ['idMecanico' => 6, 'idEspecialidad' => 4], // Héctor → Electricidad automotriz
            ['idMecanico' => 6, 'idEspecialidad' => 5], // Héctor → Transmisión
        ];

        foreach ($asignaciones as $item) {
            DB::table('mecanico_especialidad')->updateOrInsert(
                ['idMecanico' => $item['idMecanico'], 'idEspecialidad' => $item['idEspecialidad']],
                $item
            );
        }
    }
}
