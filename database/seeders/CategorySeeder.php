<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    protected $model = Category::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Filtros'],
            ['name' => 'Lubricantes'],
            ['name' => 'Frenos'],
            ['name' => 'Encendido'],
            ['name' => 'Motor'],
            ['name' => 'Transmisión'],
            ['name' => 'Suspensión'],
            ['name' => 'Sistema de Enfriamiento'],
            ['name' => 'Eléctrico'],
            ['name' => 'Iluminación'],
            ['name' => 'Cristales y Espejos'],
            ['name' => 'Escape'],
            ['name' => 'Combustible'],
            ['name' => 'Sensores'],
            ['name' => 'Catalizadores'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
