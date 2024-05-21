<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    protected $model = Vehicle::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['plate' => 'ABC123', 'km' => 15000, 'year' => 2020, 'model' => 'Model 1', 'chasis' => 'Chasis 1', 'motor' => 'Motor 1', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'DEF456', 'km' => 20000, 'year' => 2021, 'model' => 'Model 2', 'chasis' => 'Chasis 2', 'motor' => 'Motor 2', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'GHI789', 'km' => 25000, 'year' => 2022, 'model' => 'Model 3', 'chasis' => 'Chasis 3', 'motor' => 'Motor 3', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'JKL012', 'km' => 30000, 'year' => 2023, 'model' => 'Model 4', 'chasis' => 'Chasis 4', 'motor' => 'Motor 4', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'MNO345', 'km' => 35000, 'year' => 2024, 'model' => 'Model 5', 'chasis' => 'Chasis 5', 'motor' => 'Motor 5', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
        ];

        foreach ($array as $item) {
            Vehicle::create($item);
        }
    }
}
