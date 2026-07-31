<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RepuestoSeeder extends Seeder
{
    public function run(): void
    {
        // idProveedor: 1=AutoPartes Bolivia, 2=Repuestos del Sur,
        // 3=Lubricantes Petropar, 4=Import Auto Parts, 5=Dist. Nacional de Frenos
        $repuestos = [
            // Lubricantes
            ['idProveedor' => 3, 'codigo' => 'LUB-MOT-5W30',  'nombre' => 'Aceite de motor Mobil 1 5W-30 (1L)',        'precioVenta' =>  45.00, 'marca' => 'Mobil'],
            ['idProveedor' => 3, 'codigo' => 'LUB-MOT-15W40', 'nombre' => 'Aceite de motor Castrol GTX 15W-40 (1L)',   'precioVenta' =>  38.00, 'marca' => 'Castrol'],
            ['idProveedor' => 3, 'codigo' => 'LUB-TRANS-ATF', 'nombre' => 'Aceite de transmisión Dexron III (1L)',     'precioVenta' =>  42.00, 'marca' => 'Valvoline'],
            ['idProveedor' => 3, 'codigo' => 'LUB-FRENOS-DOT4','nombre' => 'Líquido de frenos DOT 4 (500ml)',          'precioVenta' =>  28.00, 'marca' => 'Bosch'],

            // Filtros
            ['idProveedor' => 1, 'codigo' => 'FIL-ACE-TOYOTA','nombre' => 'Filtro de aceite Toyota Corolla / Hilux',  'precioVenta' =>  35.00, 'marca' => 'Toyota Genuine'],
            ['idProveedor' => 1, 'codigo' => 'FIL-ACE-CHEVROLET','nombre' => 'Filtro de aceite Chevrolet Aveo/Spark', 'precioVenta' =>  30.00, 'marca' => 'AC Delco'],
            ['idProveedor' => 1, 'codigo' => 'FIL-AIRE-TOY',  'nombre' => 'Filtro de aire Toyota Corolla 2018-2024',  'precioVenta' =>  55.00, 'marca' => 'Toyota Genuine'],
            ['idProveedor' => 1, 'codigo' => 'FIL-COMB-UNIV', 'nombre' => 'Filtro de combustible universal (gasolina)','precioVenta' => 40.00, 'marca' => 'Mann Filter'],

            // Frenos
            ['idProveedor' => 5, 'codigo' => 'FRE-PAST-TOY-D','nombre' => 'Pastillas de freno delantera Toyota Corolla','precioVenta' => 120.00, 'marca' => 'Brembo'],
            ['idProveedor' => 5, 'codigo' => 'FRE-PAST-TOY-T','nombre' => 'Pastillas de freno trasera Toyota Corolla', 'precioVenta' =>  95.00, 'marca' => 'Brembo'],
            ['idProveedor' => 5, 'codigo' => 'FRE-PAST-CHEV', 'nombre' => 'Pastillas de freno delantera Chevrolet Aveo','precioVenta' =>100.00, 'marca' => 'Ferodo'],
            ['idProveedor' => 5, 'codigo' => 'FRE-DISCO-TOY', 'nombre' => 'Disco de freno delantero Toyota Corolla',   'precioVenta' => 180.00, 'marca' => 'DBA'],

            // Correas y distribución
            ['idProveedor' => 4, 'codigo' => 'COR-DIST-TOY',  'nombre' => 'Kit correa de distribución Toyota 1NZ-FE', 'precioVenta' => 280.00, 'marca' => 'Gates'],
            ['idProveedor' => 4, 'codigo' => 'COR-DIST-VW',   'nombre' => 'Kit correa de distribución VW 1.6L',       'precioVenta' => 250.00, 'marca' => 'Continental'],
            ['idProveedor' => 4, 'codigo' => 'COR-SERP-CHEV', 'nombre' => 'Correa serpentina Chevrolet Aveo',         'precioVenta' =>  65.00, 'marca' => 'Gates'],

            // Suspensión
            ['idProveedor' => 2, 'codigo' => 'SUS-AMOR-DEL',  'nombre' => 'Amortiguador delantero Toyota Corolla (c/u)','precioVenta' => 220.00,'marca' => 'Monroe'],
            ['idProveedor' => 2, 'codigo' => 'SUS-AMOR-TRA',  'nombre' => 'Amortiguador trasero Toyota Corolla (c/u)', 'precioVenta' => 180.00, 'marca' => 'Monroe'],
            ['idProveedor' => 2, 'codigo' => 'SUS-ROTULA',    'nombre' => 'Rótula de dirección universal (c/u)',       'precioVenta' =>  75.00, 'marca' => 'Moog'],
            ['idProveedor' => 2, 'codigo' => 'SUS-TERMINAL',  'nombre' => 'Terminal de dirección (c/u)',               'precioVenta' =>  60.00, 'marca' => 'Moog'],

            // Eléctricos
            ['idProveedor' => 4, 'codigo' => 'ELE-BUJIA-NGK', 'nombre' => 'Bujía NGK Iridium (c/u)',                  'precioVenta' =>  45.00, 'marca' => 'NGK'],
            ['idProveedor' => 4, 'codigo' => 'ELE-BOBINA',    'nombre' => 'Bobina de encendido Toyota 1ZZ-FE',        'precioVenta' => 195.00, 'marca' => 'Denso'],
            ['idProveedor' => 4, 'codigo' => 'ELE-BATERIA-60','nombre' => 'Batería Bosch S4 60Ah 12V',                'precioVenta' => 420.00, 'marca' => 'Bosch'],

            // Clutch
            ['idProveedor' => 2, 'codigo' => 'CLI-KIT-TOY',   'nombre' => 'Kit de embrague Toyota Corolla 1.8L',      'precioVenta' => 550.00, 'marca' => 'LuK'],
            ['idProveedor' => 2, 'codigo' => 'CLI-KIT-CHEV',  'nombre' => 'Kit de embrague Chevrolet Aveo 1.6L',      'precioVenta' => 480.00, 'marca' => 'LuK'],

            // Gases y refrigerantes
            ['idProveedor' => 3, 'codigo' => 'GAS-R134A-1K',  'nombre' => 'Gas refrigerante R-134a (1 kg)',           'precioVenta' =>  90.00, 'marca' => 'Dupont'],
            ['idProveedor' => 3, 'codigo' => 'LUB-REFRIG-1L', 'nombre' => 'Refrigerante anticongelante verde (1L)',   'precioVenta' =>  32.00, 'marca' => 'Prestone'],
        ];

        foreach ($repuestos as $repuesto) {
            DB::table('repuesto')->updateOrInsert(['codigo' => $repuesto['codigo']], $repuesto);
        }
    }
}
