<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleModelSeeder extends Seeder
{
    public function run()
    {
        $brands = Brand::where('type', 'vehicle')->get();

        $brands->each(function ($brand) {
            VehicleModel::factory()->count(5)->create([
                'brand_id' => $brand->id
            ]);
        });

    }
}
