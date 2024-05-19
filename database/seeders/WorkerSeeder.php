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
                'occupation' => 'Prueba',
                'person_id' => 2, 
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
