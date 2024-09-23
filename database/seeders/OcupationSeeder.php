<?php

namespace Database\Seeders;

use App\Models\Ocupation;
use Illuminate\Database\Seeder;

class OcupationSeeder extends Seeder
{
    protected $model = Ocupation::class;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Administrador', 'comment' => 'Default'],
            ['name' => 'Proveedor', 'comment' => 'Default'],
            ['name' => 'Mecanico', 'comment' => 'Default'],
            ['name' => 'Asesor', 'comment' => 'Default'],
            ['name' => 'Cajero', 'comment' => 'Default'],

        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
