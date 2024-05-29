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
            ['plate' => 'ABC1234', 'km' => 15000, 'year' => 2020, 'model' => 'Mercedes-Benz S-Class', 'chasis' => 'WDD2210561A123456', 'motor' => 'V8 Biturbo', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'XYZ5678', 'km' => 20000, 'year' => 2021, 'model' => 'Land Rover Range Rover', 'chasis' => 'SALGS2SV6FA123456', 'motor' => 'Ingenium 6-cylinder', 'person_id' => 1, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'GHI789', 'km' => 25000, 'year' => 2022, 'model' => 'Ford Mustang', 'chasis' => 'Chasis 3', 'motor' => 'Motor 3', 'person_id' => 2, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'JKL012', 'km' => 30000, 'year' => 2023, 'model' => 'BMW 7 Series xDrive', 'chasis' => 'Chasis 4', 'motor' => 'Motor 4', 'person_id' => 2, 'typeVehicle_id' => 1, 'brand_id' => 1],
            ['plate' => 'MNO345', 'km' => 35000, 'year' => 2024, 'model' => 'Honda Civic', 'chasis' => 'Chasis 5', 'motor' => 'Motor 5', 'person_id' => 3, 'typeVehicle_id' => 1, 'brand_id' => 1],
        ];

        foreach ($array as $item) {
            Vehicle::create($item);
        }
    }
}
