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
        /**
         * 'name',
         * 'quantity',
         * 'saleprice',
         * 'time'
         */
        $array = [
            ['name' => 'Cambio de Aceite', 'quantity' => 1, 'saleprice' => 50.00, 'time' =>2, 'specialty_id' => 1],
            ['name' => 'Alineación y Balanceo', 'quantity' => 1, 'saleprice' => 70.00, 'time' => 2, 'specialty_id' => 2],
            ['name' => 'Revisión de Frenos', 'quantity' => 1, 'saleprice' => 90.00, 'time' => 2, 'specialty_id' => 1],
            ['name' => 'Cambio de Bujías', 'quantity' => 1, 'saleprice' => 120.00, 'time' => 2, 'specialty_id' => 2],
            ['name' => 'Diagnóstico de Motor', 'quantity' => 1, 'saleprice' => 150.00, 'time' => 2, 'specialty_id' => 3],
            ['name' => 'Cambio de Batería', 'quantity' => 1, 'saleprice' => 100.00, 'time' => 2, 'specialty_id' => 1],
            ['name' => 'Reparación de Transmisión', 'quantity' => 1, 'saleprice' => 600.00, 'time' => 2, 'specialty_id' => 3],
            ['name' => 'Reparación del Sistema de Escape', 'quantity' => 1, 'saleprice' => 300.00, 'time' => 2, 'specialty_id' => 2],
            ['name' => 'Revisión del Sistema de Enfriamiento', 'quantity' => 1, 'saleprice' => 200.00, 'time' => 2, 'specialty_id' => 3],
            ['name' => 'Reemplazo de Filtro de Aire', 'quantity' => 1, 'saleprice' => 30.00, 'time' => 2, 'specialty_id' => 1],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
