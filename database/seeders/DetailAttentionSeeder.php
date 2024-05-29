<?php

namespace Database\Seeders;

use App\Models\DetailAttention;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailAttentionSeeder extends Seeder
{
    protected $model = DetailAttention::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * saleprice
         * type
         * comment
         * status
         * dateRegister
         * dateMax
         * worker_id
         * product_id
         * service_id
         * attention_id = 1
         */
        $array = [
            [
                'saleprice' => 100.00, 'type' => 'Service', 'comment' => 'Some comment here.', 'status' => 'Generada',
                'dateRegister' => '2024-05-21', 'dateMax' => '2024-05-22', 'worker_id' => 1, 'product_id' => null, 'service_id' => 1, 'attention_id' => 1
            ],
            [
                'saleprice' => 100.00, 'type' => 'Service', 'comment' => 'Some comment here.', 'status' => 'Generada',
                'dateRegister' => '2024-05-21', 'dateMax' => '2024-05-22', 'worker_id' => 1, 'product_id' => null, 'service_id' => 2, 'attention_id' => 1
            ],
            [
                'saleprice' => 100.00, 'type' => 'Service', 'comment' => 'Some comment here.', 'status' => 'Generada',
                'dateRegister' => '2024-05-21', 'dateMax' => '2024-05-22', 'worker_id' => 1, 'product_id' => null, 'service_id' => 3, 'attention_id' => 1
            ],
            [
                'saleprice' => 100.00, 'type' => 'Product', 'comment' => 'Some comment here.', 'status' => 'Generada',
                'dateRegister' => '2024-05-21', 'dateMax' => '2024-05-22', 'worker_id' => null, 'product_id' => 1, 'service_id' => null, 'attention_id' => 1
            ],
            [
                'saleprice' => 100.00, 'type' => 'Product', 'comment' => 'Some comment here.', 'status' => 'Generada',
                'dateRegister' => '2024-05-21', 'dateMax' => '2024-05-22', 'worker_id' => null, 'product_id' => 2, 'service_id' => null, 'attention_id' => 1
            ]
        ];

        foreach ($array as $data) {
            DetailAttention::create($data);
        }
    }
}
