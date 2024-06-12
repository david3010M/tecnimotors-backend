<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{

    public function run()
    {
        $array = [
            [
                'description' => 'Task 1',
                'status' => 'hacer',
//                'percentage' => 100,
//                'dateRegister' => '2024-06-07',
//                'dateStart' => '2024-06-07',
//                'dateEnd' => '2024-06-07',
                'limitDate' => '2024-06-07',
                'worker_id' => 1,
                'detail_attentions_id' => 1
            ],
            [
                'description' => 'Task 2',
                'status' => 'hacer',
//                'percentage' => 100,
//                'dateRegister' => '2024-06-07',
//                'dateStart' => '2024-06-07',
//                'dateEnd' => '2024-06-07',
                'limitDate' => '2024-06-07',
                'worker_id' => 1,
                'detail_attentions_id' => 1
            ],
            [
                'description' => 'Task 3',
                'status' => 'hacer',
//                'percentage' => 100,
//                'dateRegister' => '2024-06-07',
//                'dateStart' => '2024-06-07',
//                'dateEnd' => '2024-06-07',
                'limitDate' => '2024-06-07',
                'worker_id' => 1,
                'detail_attentions_id' => 1
            ],
            [
                'description' => 'Task 4',
                'status' => 'hacer',
//                'percentage' => 100,
//                'dateRegister' => '2024-06-07',
//                'dateStart' => '2024-06-07',
//                'dateEnd' => '2024-06-07',
                'limitDate' => '2024-06-07',
                'worker_id' => 1,
                'detail_attentions_id' => 1
            ]
        ];

        foreach ($array as $data) {
            Task::create($data);
        }
    }
}
