<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    protected $model = Brand::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
//        ARRAY OF BRANDS
            ['name' => 'Brand 1', 'type' => 'vehicle'],
            ['name' => 'Brand 2', 'type' => 'vehicle'],
            ['name' => 'Brand 3', 'type' => 'vehicle'],
            ['name' => 'Brand 4', 'type' => 'vehicle'],
            ['name' => 'Brand 5', 'type' => 'vehicle'],
            ['name' => 'Brand 6', 'type' => 'vehicle'],
            ['name' => 'Brand 7', 'type' => 'vehicle'],
            ['name' => 'Brand 8', 'type' => 'vehicle'],
            ['name' => 'Brand 9', 'type' => 'vehicle'],
            ['name' => 'Brand 10', 'type' => 'vehicle'],
            ['name' => 'Brand 11', 'type' => 'product'],
            ['name' => 'Brand 12', 'type' => 'product'],
            ['name' => 'Brand 13', 'type' => 'product'],
            ['name' => 'Brand 14', 'type' => 'product'],
            ['name' => 'Brand 15', 'type' => 'product'],
            ['name' => 'Brand 16', 'type' => 'product'],
            ['name' => 'Brand 17', 'type' => 'product'],
            ['name' => 'Brand 18', 'type' => 'product'],
            ['name' => 'Brand 19', 'type' => 'product'],
            ['name' => 'Brand 20', 'type' => 'product']
        ];

        foreach ($array as $item) {
            Brand::create($item);
        }
    }
}
