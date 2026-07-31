<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = [
            ['idSucursal' => 1, 'nombre' => 'Sucursal Central (La Paz)', 'direccion' => 'Av. Arce #1234', 'telefono' => '2244-5566', 'ciudad' => 'La Paz'],
            ['idSucursal' => 2, 'nombre' => 'Sucursal Cochabamba', 'direccion' => 'Av. América #456', 'telefono' => '4422-7788', 'ciudad' => 'Cochabamba'],
            ['idSucursal' => 3, 'nombre' => 'Sucursal Santa Cruz', 'direccion' => 'Av. Banzer Km 2', 'telefono' => '3344-9900', 'ciudad' => 'Santa Cruz'],
        ];

        foreach ($sucursales as $sucursal) {
            \Illuminate\Support\Facades\DB::table('sucursal')->updateOrInsert(
                ['idSucursal' => $sucursal['idSucursal']],
                $sucursal
            );
        }
    }
}
