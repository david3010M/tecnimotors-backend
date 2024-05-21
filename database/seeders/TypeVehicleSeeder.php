<?php

namespace Database\Seeders;

use App\Models\TypeVehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeVehicleSeeder extends Seeder
{
    protected $model = TypeVehicle::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Automóvil'],
            ['name' => 'Camioneta'],
            ['name' => 'Camión'],
            ['name' => 'Motocicleta'],
            ['name' => 'Bicicleta'],
            ['name' => 'Triciclo'],
            ['name' => 'Cuatrimoto']
        ];

        foreach ($array as $object) {
            $typeVehicle = TypeVehicle::where('name', $object['name'])->first();
            if ($typeVehicle) {
                $typeVehicle->update($object);
            } else {
                TypeVehicle::create($object);
            }
        }
    }
}
