<?php

namespace Database\Seeders;

use App\Models\Attention;
use Illuminate\Database\Seeder;

class AttentionSeeder extends Seeder
{
    protected $model = Attention::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         *
         * number
         * arrivalDate
         * deliveryDate
         * observations
         * fuelLevel
         * km
         *
         * totalService
         * totalProducts
         * total
         * debtAmount
         *
         * routeImage
         * vehicle_id
         * worker_id
         *
         */
        $array = [
            [
                'number' => 'OTRS-00000001',
                'arrivalDate' => '2024-05-21 04:09:25',
                'deliveryDate' => '2024-05-22 04:09:25',
                'observations' => 'Se reemplazarán las bujías y el filtro de aire, se ajustará la alineación y balanceo de las ruedas, y se revisará el sistema de escape. Además, se realizará un cambio de aceite y filtro.',
                'fuelLevel' => 6,
                'km' => 15000,
                'totalService' => 100.00,
                'totalProducts' => 200.00,
                'total' => 300.00,
                'debtAmount' => 100.00,
                'routeImage' => 'image.jpg',
                'vehicle_id' => 1,
                'worker_id' => 5,
            ],
            [
                'number' => 'OTRS-00000002',
                'arrivalDate' => '2024-05-21 04:09:25',
                'deliveryDate' => '2024-05-22 04:09:25',
                'observations' => 'Se procederá a la sustitución de la correa de distribución, se revisarán los niveles de fluidos y se completarán si es necesario, y se realizará una limpieza del sistema de inyección de combustible.',
                'fuelLevel' => 6,
                'km' => 15000,
                'totalService' => 100.00,
                'totalProducts' => 200.00,
                'total' => 300.00,
                'debtAmount' => 100.00,
                'routeImage' => 'image.jpg',
                'vehicle_id' => 2,
                'worker_id' => 5,
            ],
            [
                'number' => 'OTRS-00000003',
                'arrivalDate' => '2024-05-21 04:09:25',
                'deliveryDate' => '2024-05-22 04:09:25',
                'observations' => 'Se cambiarán los neumáticos debido al desgaste, se revisará y ajustará el sistema de frenos, y se realizará una inspección completa del sistema de transmisión. También se verificará la correcta operación del aire acondicionado.',
                'fuelLevel' => 6,
                'km' => 15000,
                'totalService' => 100.00,
                'totalProducts' => 200.00,
                'total' => 300.00,
                'debtAmount' => 100.00,
                'routeImage' => 'image.jpg',
                'vehicle_id' => 3,
                'worker_id' => 5,
            ],
            [
                'number' => 'OTRS-00000004',
                'arrivalDate' => '2024-05-21 04:09:25',
                'deliveryDate' => '2024-05-22 04:09:25',
                'observations' => 'Se llevará a cabo la reparación de la fuga en el radiador, se revisará y recargará el sistema de refrigeración, y se reemplazará el termostato para asegurar un funcionamiento óptimo del motor.',
                'fuelLevel' => 6,
                'km' => 15000,
                'totalService' => 100.00,
                'totalProducts' => 200.00,
                'total' => 300.00,
                'debtAmount' => 100.00,
                'routeImage' => 'image.jpg',
                'vehicle_id' => 4,
                'worker_id' => 5,
            ],
            [
                'number' => 'OTRS-00000005',
                'arrivalDate' => '2024-05-21 04:09:25',
                'deliveryDate' => '2024-05-22 04:09:25',
                'observations' => 'Se sustituirán los amortiguadores traseros, se realizará un ajuste en la suspensión delantera, y se llevará a cabo una inspección detallada del sistema de dirección. Además, se cambiará el líquido de frenos.',
                'fuelLevel' => 6,
                'km' => 15000,
                'totalService' => 100.00,
                'totalProducts' => 200.00,
                'total' => 300.00,
                'debtAmount' => 100.00,
                'routeImage' => 'image.jpg',
                'vehicle_id' => 5,
                'worker_id' => 5,
            ],
        ];

        foreach ($array as $item) {
            Attention::create($item);
        }
    }
}
