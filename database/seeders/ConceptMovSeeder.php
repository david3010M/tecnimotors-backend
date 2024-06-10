<?php

namespace Database\Seeders;

use App\Models\ConceptMov;
use Illuminate\Database\Seeder;

class ConceptMovSeeder extends Seeder
{
    protected $model = ConceptMov::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Venta'],
            ['name' => 'Compra'],
            ['name' => 'Documento Almacen'],

        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
