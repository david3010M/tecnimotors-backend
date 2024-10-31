<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    protected $model = Service::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Cambio de Aceite', 'quantity' => 1, 'saleprice' => 50.00, 'time' => 2, 'specialty_id' => 1],
            ['name' => 'Alineación y Balanceo', 'quantity' => 1, 'saleprice' => 70.00, 'time' => 2, 'specialty_id' => 2],
           
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
