<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            PermisoSeeder::class,
            SucursalSeeder::class,
            UsuarioSeeder::class,
            ClienteSeeder::class,
            MecanicoSeeder::class,
            EspecialidadSeeder::class,
            MecanicoEspecialidadSeeder::class,
            TipoServicioSeeder::class,
            ServicioSeeder::class,
            ProveedorSeeder::class,
            RepuestoSeeder::class,
            MarcaVehiculoSeeder::class,
            ModeloVehiculoSeeder::class,
            VehiculoSeeder::class,
            InventarioSeeder::class,
            MetodoPagoSeeder::class,
            CitaSeeder::class,
            OrdenTrabajoSeeder::class,
            DetalleOrdenTrabajoSeeder::class,
            DiagnosticoSeeder::class,
            FacturaSeeder::class,
            PagoSeeder::class,
            MovimientoInventarioSeeder::class,
        ]);
    }
}
