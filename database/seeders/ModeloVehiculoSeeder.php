<?php

namespace Database\Seeders;

use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use Illuminate\Database\Seeder;

class ModeloVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $modelosPorMarca = [
            'Toyota' => [
                ['nombre' => 'Corolla', 'tipoMotor' => 'Gasolina 1.8L'],
                ['nombre' => 'Hilux', 'tipoMotor' => 'Diésel 2.4L'],
            ],
            'Chevrolet' => [
                ['nombre' => 'Aveo', 'tipoMotor' => 'Gasolina 1.6L'],
                ['nombre' => 'Spark', 'tipoMotor' => 'Gasolina 1.2L'],
            ],
            'Volkswagen' => [
                ['nombre' => 'Gol', 'tipoMotor' => 'Gasolina 1.6L'],
            ],
            'Nissan' => [
                ['nombre' => 'Versa', 'tipoMotor' => 'Gasolina 1.6L'],
            ],
        ];

        foreach ($modelosPorMarca as $nombreMarca => $modelos) {
            $marca = MarcaVehiculo::where('nombre', $nombreMarca)->first();
            if (!$marca) {
                continue;
            }

            foreach ($modelos as $modelo) {
                ModeloVehiculo::firstOrCreate(
                    ['idMarca' => $marca->idMarca, 'nombre' => $modelo['nombre']],
                    ['tipoMotor' => $modelo['tipoMotor']]
                );
            }
        }
    }
}
