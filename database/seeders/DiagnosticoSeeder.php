<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosticoSeeder extends Seeder
{
    public function run(): void
    {
        $diagnosticos = [
            ['idOrden' => 2, 'descripcion' => 'Revisión de pastillas de freno y discos por ruido reportado. Se constata desgaste severo en pastillas delanteras, discos con rayaduras que requieren rectificación.', 'fecha' => '2026-06-10 14:00:00'],
            ['idOrden' => 3, 'descripcion' => 'Se conecta scanner OBD-II. Código P0135: Circuito del calentador del sensor de oxígeno. Se revisa cableado y está en buen estado, el sensor requiere reemplazo pronto.', 'fecha' => '2026-06-15 10:30:00'],
            ['idOrden' => 6, 'descripcion' => 'Prueba de compresión de cilindros. Cilindro 2 marca 60 psi (muy bajo). Se sospecha de junta de culata soplada. Se requiere desarmar para confirmar daños internos.', 'fecha' => '2026-07-06 11:00:00'],
            ['idOrden' => 10, 'descripcion' => 'El vehículo no arranca. Se revisó batería (ok 12.6V) y motor de arranque. No llega señal del inmovilizador. Se debe revisar módulo de alarma.', 'fecha' => '2026-07-22 16:30:00'],
        ];

        foreach ($diagnosticos as $diag) {
            DB::table('diagnostico')->insert($diag);
        }
    }
}
