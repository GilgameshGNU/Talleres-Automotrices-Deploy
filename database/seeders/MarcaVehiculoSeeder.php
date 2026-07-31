<?php

namespace Database\Seeders;

use App\Models\MarcaVehiculo;
use Illuminate\Database\Seeder;

class MarcaVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Toyota', 'paisOrigen' => 'Japón'],
            ['nombre' => 'Chevrolet', 'paisOrigen' => 'Estados Unidos'],
            ['nombre' => 'Volkswagen', 'paisOrigen' => 'Alemania'],
            ['nombre' => 'Nissan', 'paisOrigen' => 'Japón'],
        ];

        foreach ($marcas as $marca) {
            MarcaVehiculo::firstOrCreate(['nombre' => $marca['nombre']], $marca);
        }
    }
}
