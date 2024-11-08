<?php

namespace Database\Seeders;

use App\Models\NoteReason;
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
            ['code' => '3', 'description' => 'Correción por error en la descripción'],
            ['code' => '4', 'description' => 'Descuento Global'],
            ['code' => '5', 'description' => 'Descuento por ítem'],
            ['code' => '6', 'description' => 'Devolución total'],
            ['code' => '7', 'description' => 'Devolución por ítem'],
            ['code' => '8', 'description' => 'Bonificación'],
            ['code' => '9', 'description' => 'Disminución en el valor'],
            ['code' => '10', 'description' => 'Otros conceptos'],
        ];

        foreach ($noteReasons as $noteReason) {
            NoteReason::create($noteReason);
        }
    }
}
