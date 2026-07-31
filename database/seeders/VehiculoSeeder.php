<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        // Modelos disponibles (del ModeloVehiculoSeeder):
        // Toyota:     1=Corolla(Gasolina 1.8L), 2=Hilux(Diésel 2.4L)
        // Chevrolet:  3=Aveo(Gasolina 1.6L),   4=Spark(Gasolina 1.2L)
        // Volkswagen: 5=Gol(Gasolina 1.6L)
        // Nissan:     6=Versa(Gasolina 1.6L)
        //
        // idCliente: 1..10 (del ClienteSeeder)
        $vehiculos = [
            // Mario Quispe (cliente 1)
            ['idCliente' => 1, 'idModelo' => 1, 'placa' => '2345-ABC', 'anio' => 2021, 'color' => 'Blanco',    'kilometraje' =>  38500],
            ['idCliente' => 1, 'idModelo' => 2, 'placa' => '1234-LPZ', 'anio' => 2019, 'color' => 'Plata',     'kilometraje' =>  72000],

            // Ana Flores (cliente 2)
            ['idCliente' => 2, 'idModelo' => 3, 'placa' => '3456-CBB', 'anio' => 2020, 'color' => 'Rojo',      'kilometraje' =>  54000],

            // Pedro Mamani (cliente 3)
            ['idCliente' => 3, 'idModelo' => 4, 'placa' => '4567-CBB', 'anio' => 2022, 'color' => 'Negro',     'kilometraje' =>  18000],

            // Lucía García (cliente 4)
            ['idCliente' => 4, 'idModelo' => 5, 'placa' => '5678-SCZ', 'anio' => 2018, 'color' => 'Gris',      'kilometraje' =>  95000],

            // Carlos Romero (cliente 5)
            ['idCliente' => 5, 'idModelo' => 6, 'placa' => '6789-LPZ', 'anio' => 2023, 'color' => 'Azul',      'kilometraje' =>  12000],

            // Rosa Condori (cliente 6)
            ['idCliente' => 6, 'idModelo' => 1, 'placa' => '7890-LPZ', 'anio' => 2017, 'color' => 'Blanco',    'kilometraje' => 130000],

            // Jorge Aliaga (cliente 7)
            ['idCliente' => 7, 'idModelo' => 2, 'placa' => '8901-ORU', 'anio' => 2016, 'color' => 'Plata',     'kilometraje' => 158000],

            // Beatriz Zenteno (cliente 8)
            ['idCliente' => 8, 'idModelo' => 3, 'placa' => '9012-SCZ', 'anio' => 2021, 'color' => 'Blanco',    'kilometraje' =>  42000],

            // Raúl Chávez (cliente 9)
            ['idCliente' => 9, 'idModelo' => 6, 'placa' => '1023-TJA', 'anio' => 2020, 'color' => 'Rojo',      'kilometraje' =>  61000],

            // Sandra Vega (cliente 10)
            ['idCliente' => 10,'idModelo' => 4, 'placa' => '2034-LPZ', 'anio' => 2022, 'color' => 'Verde',     'kilometraje' =>  27000],
        ];

        foreach ($vehiculos as $vehiculo) {
            DB::table('vehiculo')->updateOrInsert(['placa' => $vehiculo['placa']], $vehiculo);
        }
    }
}
