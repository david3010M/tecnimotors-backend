<?php

namespace Database\Seeders;

use App\Models\Unit;
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
            ['name' => 'Unidad', 'code' => 'UN'],
            ['name' => 'Pieza', 'code' => 'PZ'],
            ['name' => 'Litro', 'code' => 'LT'],
            ['name' => 'Juego', 'code' => 'JG'],
            ['name' => 'Metro', 'code' => 'MT'],
            ['name' => 'Galón', 'code' => 'GL'],
            ['name' => 'Kit', 'code' => 'KT'],
            ['name' => 'Par', 'code' => 'PR'],
            ['name' => 'Botella', 'code' => 'BT'],
            ['name' => 'Caja', 'code' => 'CJ'],

        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
