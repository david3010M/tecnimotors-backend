<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $noteReasons = [
            ['code' => '1', 'description' => 'Anulación de la Operación'],
            ['code' => '2', 'description' => 'Anulación por error en el monto'],
        ];
    }
}
