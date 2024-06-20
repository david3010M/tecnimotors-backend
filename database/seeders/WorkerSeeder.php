<?php

namespace Database\Seeders;

use App\Models\Worker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'id' => 1,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Administrador',
                'person_id' => 1,
            ],
            [
                'id' => 2,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Proveedor',
                'person_id' => 3,
            ],
            [
                'id' => 3,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Mecanico',
                'person_id' => 4,
            ],
            [
                'id' => 4,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Mecanico',
                'person_id' => 5,
            ],
            [
                'id' => 5,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Mecanico',
                'person_id' => 6,
            ],
            [
                'id' => 6,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Cajero',
                'person_id' => 7,
            ],
            [
                'id' => 7,
                'startDate' => null,
                'birthDate' => null,
                'occupation' => 'Asesor',
                'person_id' => 8,
            ],
        ];

        foreach ($array as $data) {
            $worker = Worker::find($data['id']);
            if ($worker) {
                $worker->update($data); // Corregido: Pasando el arreglo de datos en lugar del objeto
            } else {
                Worker::create($data);
            }
        }
    }

}
