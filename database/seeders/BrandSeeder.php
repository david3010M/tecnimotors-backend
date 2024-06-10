<?php

namespace Database\Seeders;

use App\Models\Brand;
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

            ['name' => 'Bosch', 'type' => 'product'],
            ['name' => 'Denso', 'type' => 'product'],
            ['name' => 'Mann-Filter', 'type' => 'product'],
            ['name' => 'ACDelco', 'type' => 'product'],
            ['name' => 'NGK', 'type' => 'product'],
            ['name' => 'Brembo', 'type' => 'product'],
            ['name' => 'Mobil 1', 'type' => 'product'],
            ['name' => 'Castrol', 'type' => 'product'],
            ['name' => 'Continental', 'type' => 'product'],
            ['name' => 'Philips', 'type' => 'product'],
            ['name' => 'Valeo', 'type' => 'product'],
            ['name' => 'Monroe', 'type' => 'product'],
            ['name' => 'KYB', 'type' => 'product'],
            ['name' => 'Delphi', 'type' => 'product'],
            ['name' => 'Hella', 'type' => 'product'],

            ['name' => 'Mercedes-Benz', 'type' => 'vehicle'],
            ['name' => 'Land Rover', 'type' => 'vehicle'],
            ['name' => 'Porsche', 'type' => 'vehicle'],
            ['name' => 'Chevrolet', 'type' => 'vehicle'],
            ['name' => 'Jeep', 'type' => 'vehicle'],
        ];

        foreach ($array as $item) {
            Brand::create($item);
        }
    }
}
