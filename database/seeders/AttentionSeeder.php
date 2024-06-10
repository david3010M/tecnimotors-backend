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
                'km' => 500,
                'totalService' => 200.00,
                'totalProducts' => 100.00,
                'total' => 300.00,
                'debtAmount' => 0.00,

                'routeImage' => 'image.jpg',
                'vehicle_id' => 1,
                'worker_id' => 5,
            ],
        ];

        foreach ($array as $item) {
            Attention::create($item);
        }
    }
}
