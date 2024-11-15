<?php

namespace Database\Seeders;

use App\Models\GuideMotive;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuideMotiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $motive = [
            ['code' => '01', 'name' => 'VENTA'],
            ['code' => '02', 'name' => 'COMPRA'],
            ['code' => '04', 'name' => 'TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA'],
            ['code' => '08', 'name' => 'IMPORTACION'],
            ['code' => '09', 'name' => 'EXPORTACION'],
            ['code' => '13', 'name' => 'OTROS'],
            ['code' => '14', 'name' => 'VENTA SUJETA A CONFIRMACIÓN DEL COMPRADOR'],
            ['code' => '18', 'name' => 'TRASLADO EMISOR ITINERANTE CP'],
            ['code' => '19', 'name' => 'TRASLADO A ZONA PRIMARIA'],
        ];

        foreach ($motive as $item) {
            GuideMotive::create($item);
        }
    }
}
