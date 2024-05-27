<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    protected $model = Specialty::class;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Generalista'],
            ['name' => 'Frenista'],
            ['name' => 'Transmisionista'],
            ['name' => 'Motorista'],
            ['name' => 'Suspensionista'],
            ['name' => 'Electricista'],
            ['name' => 'Diagnóstico'],
            ['name' => 'Climatización'],
            ['name' => 'Carrocero'],
            ['name' => 'Llantero'],
            ['name' => 'Escapista'],
            ['name' => 'Enfriamiento'],
            ['name' => 'Emisiones'],
            ['name' => 'Preventivo'],
            ['name' => 'Accesorista'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
