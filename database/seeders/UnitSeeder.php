<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    protected $model = Unit::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Unidad 1', 'code' => 'U1'],
            ['name' => 'Unidad 2', 'code' => 'U2'],
            ['name' => 'Unidad 3', 'code' => 'U3'],
            ['name' => 'Unidad 4', 'code' => 'U4'],
            ['name' => 'Unidad 5', 'code' => 'U5'],
            ['name' => 'Unidad 6', 'code' => 'U6'],
            ['name' => 'Unidad 7', 'code' => 'U7'],
            ['name' => 'Unidad 8', 'code' => 'U8'],
            ['name' => 'Unidad 9', 'code' => 'U9'],
            ['name' => 'Unidad 10', 'code' => 'U10']
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
