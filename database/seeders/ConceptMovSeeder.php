<?php

namespace Database\Seeders;

use App\Models\ConceptMov;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['name' => 'Ingreso'],
            ['name' => 'Egreso'],
            ['name' => 'Transferencia'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
