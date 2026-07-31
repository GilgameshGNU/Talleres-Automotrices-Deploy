<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        // 25 repuestos × 3 sucursales → un registro por combinación relevante
        // idSucursal: 1=La Paz, 2=Cochabamba, 3=Santa Cruz
        // idRepuesto: 1..25 (del RepuestoSeeder en orden de inserción)
        $inventarios = [
            // --- Sucursal La Paz (1) ---
            ['idSucursal' => 1, 'idRepuesto' =>  1, 'stockActual' => 20, 'stockMinimo' =>  5, 'ubicacion' => 'A-01'],
            ['idSucursal' => 1, 'idRepuesto' =>  2, 'stockActual' => 15, 'stockMinimo' =>  5, 'ubicacion' => 'A-02'],
            ['idSucursal' => 1, 'idRepuesto' =>  3, 'stockActual' => 10, 'stockMinimo' =>  3, 'ubicacion' => 'A-03'],
            ['idSucursal' => 1, 'idRepuesto' =>  4, 'stockActual' => 25, 'stockMinimo' =>  5, 'ubicacion' => 'A-04'],
            ['idSucursal' => 1, 'idRepuesto' =>  5, 'stockActual' => 12, 'stockMinimo' =>  4, 'ubicacion' => 'B-01'],
            ['idSucursal' => 1, 'idRepuesto' =>  6, 'stockActual' =>  8, 'stockMinimo' =>  3, 'ubicacion' => 'B-02'],
            ['idSucursal' => 1, 'idRepuesto' =>  7, 'stockActual' =>  6, 'stockMinimo' =>  2, 'ubicacion' => 'B-03'],
            ['idSucursal' => 1, 'idRepuesto' =>  8, 'stockActual' => 10, 'stockMinimo' =>  3, 'ubicacion' => 'B-04'],
            ['idSucursal' => 1, 'idRepuesto' =>  9, 'stockActual' => 14, 'stockMinimo' =>  4, 'ubicacion' => 'C-01'],
            ['idSucursal' => 1, 'idRepuesto' => 10, 'stockActual' => 10, 'stockMinimo' =>  3, 'ubicacion' => 'C-02'],
            ['idSucursal' => 1, 'idRepuesto' => 11, 'stockActual' =>  8, 'stockMinimo' =>  3, 'ubicacion' => 'C-03'],
            ['idSucursal' => 1, 'idRepuesto' => 12, 'stockActual' =>  5, 'stockMinimo' =>  2, 'ubicacion' => 'C-04'],
            ['idSucursal' => 1, 'idRepuesto' => 20, 'stockActual' => 30, 'stockMinimo' => 10, 'ubicacion' => 'D-01'],
            ['idSucursal' => 1, 'idRepuesto' => 21, 'stockActual' =>  4, 'stockMinimo' =>  2, 'ubicacion' => 'D-02'],
            ['idSucursal' => 1, 'idRepuesto' => 22, 'stockActual' =>  3, 'stockMinimo' =>  1, 'ubicacion' => 'D-03'],

            // --- Sucursal Cochabamba (2) ---
            ['idSucursal' => 2, 'idRepuesto' =>  1, 'stockActual' => 18, 'stockMinimo' =>  5, 'ubicacion' => 'A-01'],
            ['idSucursal' => 2, 'idRepuesto' =>  2, 'stockActual' => 12, 'stockMinimo' =>  4, 'ubicacion' => 'A-02'],
            ['idSucursal' => 2, 'idRepuesto' =>  5, 'stockActual' =>  9, 'stockMinimo' =>  3, 'ubicacion' => 'B-01'],
            ['idSucursal' => 2, 'idRepuesto' =>  9, 'stockActual' => 11, 'stockMinimo' =>  4, 'ubicacion' => 'B-02'],
            ['idSucursal' => 2, 'idRepuesto' => 13, 'stockActual' =>  4, 'stockMinimo' =>  2, 'ubicacion' => 'C-01'],
            ['idSucursal' => 2, 'idRepuesto' => 16, 'stockActual' =>  6, 'stockMinimo' =>  2, 'ubicacion' => 'C-02'],
            ['idSucursal' => 2, 'idRepuesto' => 17, 'stockActual' =>  5, 'stockMinimo' =>  2, 'ubicacion' => 'C-03'],
            ['idSucursal' => 2, 'idRepuesto' => 24, 'stockActual' => 10, 'stockMinimo' =>  3, 'ubicacion' => 'D-01'],
            ['idSucursal' => 2, 'idRepuesto' => 25, 'stockActual' => 20, 'stockMinimo' =>  5, 'ubicacion' => 'D-02'],

            // --- Sucursal Santa Cruz (3) ---
            ['idSucursal' => 3, 'idRepuesto' =>  1, 'stockActual' => 22, 'stockMinimo' =>  5, 'ubicacion' => 'A-01'],
            ['idSucursal' => 3, 'idRepuesto' =>  2, 'stockActual' => 16, 'stockMinimo' =>  5, 'ubicacion' => 'A-02'],
            ['idSucursal' => 3, 'idRepuesto' =>  4, 'stockActual' => 18, 'stockMinimo' =>  4, 'ubicacion' => 'A-03'],
            ['idSucursal' => 3, 'idRepuesto' =>  6, 'stockActual' =>  7, 'stockMinimo' =>  3, 'ubicacion' => 'B-01'],
            ['idSucursal' => 3, 'idRepuesto' => 10, 'stockActual' =>  9, 'stockMinimo' =>  3, 'ubicacion' => 'B-02'],
            ['idSucursal' => 3, 'idRepuesto' => 11, 'stockActual' =>  6, 'stockMinimo' =>  2, 'ubicacion' => 'B-03'],
            ['idSucursal' => 3, 'idRepuesto' => 20, 'stockActual' => 25, 'stockMinimo' =>  8, 'ubicacion' => 'C-01'],
            ['idSucursal' => 3, 'idRepuesto' => 24, 'stockActual' =>  8, 'stockMinimo' =>  3, 'ubicacion' => 'C-02'],
            ['idSucursal' => 3, 'idRepuesto' => 25, 'stockActual' => 14, 'stockMinimo' =>  4, 'ubicacion' => 'C-03'],
        ];

        foreach ($inventarios as $inventario) {
            DB::table('inventario')->updateOrInsert(
                ['idSucursal' => $inventario['idSucursal'], 'idRepuesto' => $inventario['idRepuesto']],
                $inventario
            );
        }
    }
}
