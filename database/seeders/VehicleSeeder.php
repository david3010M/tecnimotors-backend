<?php

namespace Database\Seeders;

use App\Models\Vehicle;
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
            ['plate' => 'ABC1234', 'year' => 2020, 'codeBin' => '1239', 'model' => 'S-Class', 'chasis' => 'WDD2210561A123456', 'motor' => 'V8 Biturbo', 'person_id' => 3, 'typeVehicle_id' => 1, 'vehicle_model_id' => 1],
            ['plate' => 'XYZ5678', 'year' => 2021, 'codeBin' => '1234', 'model' => 'Range Rover', 'chasis' => 'SALGS2SV6FA123456', 'motor' => 'Ingenium 6-cylinder', 'person_id' => 2, 'typeVehicle_id' => 1, 'vehicle_model_id' => 2],
            ['plate' => 'JKL9101', 'year' => 2022, 'codeBin' => '1243', 'model' => 'Panamera Turbo', 'chasis' => 'WP0ZZZ97ZHL123456', 'motor' => '4.0L Twin-Turbo V8', 'person_id' => 3, 'typeVehicle_id' => 1, 'vehicle_model_id' => 3],
            ['plate' => 'MNO2345', 'year' => 2023, 'codeBin' => '1238', 'model' => 'Silverado 1500', 'chasis' => '1GCVKREC1EZ123456', 'motor' => 'EcoTec3 V8', 'person_id' => 4, 'typeVehicle_id' => 1, 'vehicle_model_id' => 4],
            ['plate' => 'PQR6789', 'year' => 2024, 'codeBin' => '1237', 'model' => 'Grand Cherokee', 'chasis' => '1C4RJFBG9LC123456', 'motor' => '3.6L Pentastar V6', 'person_id' => 6, 'typeVehicle_id' => 1, 'vehicle_model_id' => 5],
        ];

        foreach ($array as $item) {
            Vehicle::create($item);
        }
    }
}
