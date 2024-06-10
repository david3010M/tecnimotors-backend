<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $model = Product::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'product 1', 'purchase_price' => '100.00', 'percentage' => '10.00', 'sale_price' => '110.00', 'stock' => '100', 'quantity' => '10', 'type' => 'product type', 'category_id' => '1', 'unit_id' => '1', 'brand_id' => '1'],
            ['name' => 'product 2', 'purchase_price' => '90.00', 'percentage' => '20.00', 'sale_price' => '100.00', 'stock' => '200', 'quantity' => '20', 'type' => 'product type', 'category_id' => '2', 'unit_id' => '2', 'brand_id' => '2'],
            ['name' => 'product 3', 'purchase_price' => '300.00', 'percentage' => '30.00', 'sale_price' => '330.00', 'stock' => '300', 'quantity' => '30', 'type' => 'product type', 'category_id' => '3', 'unit_id' => '3', 'brand_id' => '3'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }


    }
}
