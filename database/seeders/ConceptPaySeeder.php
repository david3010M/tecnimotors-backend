<?php

namespace Database\Seeders;

use App\Models\ConceptPay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConceptPaySeeder extends Seeder
{
    protected $model = ConceptPay::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['number' => 1, 'name' => 'ConceptPay 1', 'type' => 'Ingreso'],
            ['number' => 2, 'name' => 'ConceptPay 2', 'type' => 'Egreso'],
            ['number' => 3, 'name' => 'ConceptPay 3', 'type' => 'Transferencia'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
