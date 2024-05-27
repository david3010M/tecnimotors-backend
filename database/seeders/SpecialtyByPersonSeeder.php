<?php

namespace Database\Seeders;

use App\Models\SpecialtyPerson;
use Illuminate\Database\Seeder;

class SpecialtyByPersonSeeder extends Seeder
{
    protected $model = SpecialtyPerson::class;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['specialty_id' => 1, 'worker_id' => 4],
            ['specialty_id' => 2, 'worker_id' => 4],
            ['specialty_id' => 3, 'worker_id' => 5],
            ['specialty_id' => 1, 'worker_id' => 3],
            ['specialty_id' => 2, 'worker_id' => 3],
            ['specialty_id' => 3, 'worker_id' => 3],
            ['specialty_id' => 4, 'worker_id' => 4],

            ['specialty_id' => 5, 'worker_id' => 4],
            ['specialty_id' => 6, 'worker_id' => 4],
            ['specialty_id' => 7, 'worker_id' => 5],
            ['specialty_id' => 8, 'worker_id' => 3],
            ['specialty_id' => 9, 'worker_id' => 3],
            ['specialty_id' => 10, 'worker_id' => 3],
            ['specialty_id' => 11, 'worker_id' => 4],

        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
