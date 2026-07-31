<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $citas = [
            // Citas pasadas completadas
            ['idCliente' => 1, 'idVehiculo' => 1, 'idMecanico' => 1, 'fecha' => '2026-06-02', 'hora' => '08:30:00', 'estado' => 'Completada', 'motivo' => 'Mantenimiento de 40000 km'],
            ['idCliente' => 2, 'idVehiculo' => 3, 'idMecanico' => 3, 'fecha' => '2026-06-10', 'hora' => '09:00:00', 'estado' => 'Completada', 'motivo' => 'Revisión de frenos (ruidos)'],
            ['idCliente' => 5, 'idVehiculo' => 6, 'idMecanico' => 1, 'fecha' => '2026-07-01', 'hora' => '10:30:00', 'estado' => 'Completada', 'motivo' => 'Cambio de aceite'],

            // Citas canceladas
            ['idCliente' => 4, 'idVehiculo' => 5, 'idMecanico' => null,'fecha' => '2026-06-18', 'hora' => '14:00:00', 'estado' => 'Cancelada', 'motivo' => 'No podré asistir por viaje'],
            
            // Citas pendientes/futuras
            ['idCliente' => 1, 'idVehiculo' => 2, 'idMecanico' => null,'fecha' => '2026-07-28', 'hora' => '08:30:00', 'estado' => 'Pendiente', 'motivo' => 'Revisión general antes de viaje'],
            ['idCliente' => 3, 'idVehiculo' => 4, 'idMecanico' => 4, 'fecha' => '2026-07-29', 'hora' => '15:00:00', 'estado' => 'Confirmada', 'motivo' => 'Falla en encendido a veces'],
            ['idCliente' => 8, 'idVehiculo' => 9, 'idMecanico' => null,'fecha' => '2026-07-30', 'hora' => '10:00:00', 'estado' => 'Pendiente', 'motivo' => 'El aire acondicionado no enfría bien'],
        ];

        foreach ($citas as $cita) {
            DB::table('cita')->insert($cita);
        }
    }
}
