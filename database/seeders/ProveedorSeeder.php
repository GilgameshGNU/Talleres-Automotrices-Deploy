<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'razonSocial' => 'AutoPartes Bolivia S.R.L.',
                'nit'         => '1020304050',
                'telefono'    => '22412345',    // La Paz (fijo)
                'email'       => 'ventas@autopartesbolivia.com',
            ],
            [
                'razonSocial' => 'Repuestos del Sur S.A.',
                'nit'         => '2030405060',
                'telefono'    => '44512678',    // Cochabamba (fijo)
                'email'       => 'info@repuestosdelsur.com.bo',
            ],
            [
                'razonSocial' => 'Lubricantes Petropar Ltda.',
                'nit'         => '3040506070',
                'telefono'    => '33678901',    // Santa Cruz (fijo)
                'email'       => 'ventas@petropar.com.bo',
            ],
            [
                'razonSocial' => 'Import Auto Parts Hnos. Flores',
                'nit'         => '4050607080',
                'telefono'    => '72901234',    // Móvil Tigo
                'email'       => 'importautoparts@gmail.com',
            ],
            [
                'razonSocial' => 'Distribuidora Nacional de Frenos',
                'nit'         => '5060708090',
                'telefono'    => '22890123',    // La Paz (fijo)
                'email'       => 'contacto@dnfrenos.com.bo',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            DB::table('proveedor')->updateOrInsert(['nit' => $proveedor['nit']], $proveedor);
        }
    }
}
