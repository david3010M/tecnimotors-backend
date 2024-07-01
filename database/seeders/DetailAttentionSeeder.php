<?php

namespace Database\Seeders;

use App\Models\Attention;
use App\Models\DetailAttention;
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
//        $array = [
//            [
//                'saleprice' => 100.00, 'quantity' => 1, 'type' => 'Service', 'comment' => 'Some comment here.', 'status' => 'Generada',
//                'dateRegister' => '2024-05-21', 'dateMax' => '2024-06-22', 'worker_id' => 3, 'product_id' => null, 'service_id' => 1, 'attention_id' => 1,
//            ],
//            [
//                'saleprice' => 100.00, 'quantity' => 1, 'type' => 'Service', 'comment' => 'Some comment here.', 'status' => 'Generada',
//                'dateRegister' => '2024-05-21', 'dateMax' => '2024-06-22', 'worker_id' => 3, 'product_id' => null, 'service_id' => 2, 'attention_id' => 1,
//            ],
//
//            [
//                'saleprice' => 100.00, 'quantity' => 1, 'type' => 'Product', 'comment' => 'Some comment here.', 'status' => 'Generada',
//                'dateRegister' => '2024-05-21', 'dateMax' => '2024-06-22', 'worker_id' => null, 'product_id' => 2, 'service_id' => null, 'attention_id' => 1,
//            ],
//
//        ];

//        foreach ($array as $data) {
//            $this->model::create($data);
//        }

        $attentions = Attention::all();

        foreach ($attentions as $attention) {
            DetailAttention::factory()->count(3)->create([
                'attention_id' => $attention->id,
            ]);

//            UPDATE totalService, totalProducts AND total
            $totalService = $attention->details->where('type', 'Service')->sum('saleprice');
            $totalProducts = $attention->details->where('type', 'Product')->sum('saleprice');
            $attention->totalService = $totalService;
            $attention->totalProducts = $totalProducts;
            $attention->total = $totalService + $totalProducts;
            $attention->save();
        }


    }
}
