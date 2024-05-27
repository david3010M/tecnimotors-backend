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
            ['name' => 'Service 1', 'quantity' => 1, 'saleprice' => 100.00, 'time' => 2.5, 'specialty_id' => 1],
            ['name' => 'Service 2', 'quantity' => 2, 'saleprice' => 200.00, 'time' => 3.5, 'specialty_id' => 1],
            ['name' => 'Service 3', 'quantity' => 3, 'saleprice' => 300.00, 'time' => 4.5, 'specialty_id' => 2],
            ['name' => 'Service 4', 'quantity' => 4, 'saleprice' => 400.00, 'time' => 5.5, 'specialty_id' => 2],
            ['name' => 'Service 5', 'quantity' => 5, 'saleprice' => 500.00, 'time' => 6.5, 'specialty_id' => 1],
            ['name' => 'Service 6', 'quantity' => 6, 'saleprice' => 600.00, 'time' => 7.5, 'specialty_id' => 2],
            ['name' => 'Service 7', 'quantity' => 7, 'saleprice' => 700.00, 'time' => 8.5, 'specialty_id' => 3],
            ['name' => 'Service 8', 'quantity' => 8, 'saleprice' => 800.00, 'time' => 9.5, 'specialty_id' => 1],
            ['name' => 'Service 9', 'quantity' => 9, 'saleprice' => 900.00, 'time' => 10.5, 'specialty_id' => 3],
            ['name' => 'Service 10', 'quantity' => 10, 'saleprice' => 1000.00, 'time' => 11.5, 'specialty_id' => 3],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
