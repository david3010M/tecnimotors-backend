<?php

namespace Database\Seeders;

use App\Models\Attention;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'number' => 'OTRS-00000001', 'arrivalDate' => '2024-05-21', 'deliveryDate' => '2024-05-22',
                'observations' => 'Some observations here.', 'fuelLevel' => 80, 'km' => 15000,
                'totalService' => 100.00, 'totalProducts' => 200.00, 'total' => 300.00, 'debtAmount' => 100.00,
                'routeImage' => 'image.jpg', 'vehicle_id' => 1, 'worker_id' => 1
            ],
            [
                'number' => 'OTRS-00000002', 'arrivalDate' => '2024-05-21', 'deliveryDate' => '2024-05-22',
                'observations' => 'Some observations here.', 'fuelLevel' => 80, 'km' => 15000,
                'totalService' => 100.00, 'totalProducts' => 200.00, 'total' => 300.00, 'debtAmount' => 100.00,
                'routeImage' => 'image.jpg', 'vehicle_id' => 2, 'worker_id' => 2
            ],
            [
                'number' => 'OTRS-00000003', 'arrivalDate' => '2024-05-21', 'deliveryDate' => '2024-05-22',
                'observations' => 'Some observations here.', 'fuelLevel' => 80, 'km' => 15000,
                'totalService' => 100.00, 'totalProducts' => 200.00, 'total' => 300.00, 'debtAmount' => 100.00,
                'routeImage' => 'image.jpg', 'vehicle_id' => 3, 'worker_id' => 3
            ],
            [
                'number' => 'OTRS-00000004', 'arrivalDate' => '2024-05-21', 'deliveryDate' => '2024-05-22',
                'observations' => 'Some observations here.', 'fuelLevel' => 80, 'km' => 15000,
                'totalService' => 100.00, 'totalProducts' => 200.00, 'total' => 300.00, 'debtAmount' => 100.00,
                'routeImage' => 'image.jpg', 'vehicle_id' => 4, 'worker_id' => 4
            ],
            [
                'number' => 'OTRS-00000005', 'arrivalDate' => '2024-05-21', 'deliveryDate' => '2024-05-22',
                'observations' => 'Some observations here.', 'fuelLevel' => 80, 'km' => 15000,
                'totalService' => 100.00, 'totalProducts' => 200.00, 'total' => 300.00, 'debtAmount' => 100.00,
                'routeImage' => 'image.jpg', 'vehicle_id' => 5, 'worker_id' => 5
            ],
        ];

        foreach ($array as $item) {
            Attention::create($item);
        }
    }
}
