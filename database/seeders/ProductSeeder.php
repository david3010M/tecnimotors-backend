<?php

namespace Database\Seeders;

use App\Models\Product;
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
        $array =  [
            ['name' => 'Filtro de aire', 'purchase_price' => '50.00', 'percentage' => '20.00', 'sale_price' => '60.00', 'stock' => '150', 'quantity' => '10', 'type' => 'Repuesto', 'category_id' => '1', 'unit_id' => '1', 'brand_id' => '1'],
            ['name' => 'Aceite de motor', 'purchase_price' => '70.00', 'percentage' => '15.00', 'sale_price' => '80.50', 'stock' => '200', 'quantity' => '15', 'type' => 'Lubricante', 'category_id' => '2', 'unit_id' => '1', 'brand_id' => '2'],
            ['name' => 'Pastillas de freno', 'purchase_price' => '120.00', 'percentage' => '25.00', 'sale_price' => '150.00', 'stock' => '100', 'quantity' => '20', 'type' => 'Repuesto', 'category_id' => '3', 'unit_id' => '1', 'brand_id' => '3'],
            ['name' => 'Bujías', 'purchase_price' => '35.00', 'percentage' => '30.00', 'sale_price' => '45.50', 'stock' => '300', 'quantity' => '50', 'type' => 'Repuesto', 'category_id' => '4', 'unit_id' => '1', 'brand_id' => '4'],
            ['name' => 'Filtro de aceite', 'purchase_price' => '25.00', 'percentage' => '20.00', 'sale_price' => '30.00', 'stock' => '250', 'quantity' => '40', 'type' => 'Repuesto', 'category_id' => '5', 'unit_id' => '1', 'brand_id' => '5'],
            ['name' => 'Correa de distribución', 'purchase_price' => '150.00', 'percentage' => '20.00', 'sale_price' => '180.00', 'stock' => '80', 'quantity' => '10', 'type' => 'Repuesto', 'category_id' => '1', 'unit_id' => '6', 'brand_id' => '6'],
            ['name' => 'Amortiguadores', 'purchase_price' => '200.00', 'percentage' => '25.00', 'sale_price' => '250.00', 'stock' => '60', 'quantity' => '8', 'type' => 'Repuesto', 'category_id' => '1', 'unit_id' => '7', 'brand_id' => '7'],
            ['name' => 'Radiador', 'purchase_price' => '300.00', 'percentage' => '20.00', 'sale_price' => '360.00', 'stock' => '50', 'quantity' => '5', 'type' => 'Repuesto', 'category_id' => '8', 'unit_id' => '1', 'brand_id' => '8'],
            ['name' => 'Batería', 'purchase_price' => '250.00', 'percentage' => '15.00', 'sale_price' => '287.50', 'stock' => '70', 'quantity' => '10', 'type' => 'Repuesto', 'category_id' => '9', 'unit_id' => '1', 'brand_id' => '9'],
            ['name' => 'Alternador', 'purchase_price' => '400.00', 'percentage' => '10.00', 'sale_price' => '440.00', 'stock' => '40', 'quantity' => '6', 'type' => 'Repuesto', 'category_id' => '10', 'unit_id' => '1', 'brand_id' => '10'],
            ['name' => 'Faro delantero', 'purchase_price' => '150.00', 'percentage' => '20.00', 'sale_price' => '180.00', 'stock' => '90', 'quantity' => '12', 'type' => 'Repuesto', 'category_id' => '11', 'unit_id' => '1', 'brand_id' => '10'],
            ['name' => 'Parabrisas', 'purchase_price' => '500.00', 'percentage' => '10.00', 'sale_price' => '550.00', 'stock' => '30', 'quantity' => '5', 'type' => 'Repuesto', 'category_id' => '12', 'unit_id' => '1', 'brand_id' => '10'],
            ['name' => 'Filtro de combustible', 'purchase_price' => '40.00', 'percentage' => '25.00', 'sale_price' => '50.00', 'stock' => '200', 'quantity' => '30', 'type' => 'Repuesto', 'category_id' => '13', 'unit_id' => '1', 'brand_id' => '10'],
            ['name' => 'Sensor de oxígeno', 'purchase_price' => '90.00', 'percentage' => '20.00', 'sale_price' => '108.00', 'stock' => '150', 'quantity' => '15', 'type' => 'Repuesto', 'category_id' => '14', 'unit_id' => '1', 'brand_id' => '10'],
            ['name' => 'Catalizador', 'purchase_price' => '800.00', 'percentage' => '15.00', 'sale_price' => '920.00', 'stock' => '20', 'quantity' => '2', 'type' => 'Repuesto', 'category_id' => '15', 'unit_id' => '1', 'brand_id' => '10']
        ];
        

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
