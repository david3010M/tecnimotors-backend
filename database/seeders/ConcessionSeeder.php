<?php

namespace Database\Seeders;

use App\Models\Concession;
use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConcessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $concessionaire = Person::create([
            'typeofDocument' => 'RUC',
            'documentNumber' => '12345678901',
            'businessName' => 'H2OLMOS S.A.',
            'address' => 'VICTOR A.BELAUNDE NRO. 280 INT. 502 LIMA LIMA SAN ISIDRO',
        ]);

        $client = Person::create([
            'typeofDocument' => 'DNI',
            'documentNumber' => '20479569780',
            'businessName' => 'GOBIERNO REGIONAL LAMBAYEQUE',
            'address' => 'AV. JUAN TOMIS STACK KM. 4.5 LAMBAYEQUE CHICLAYO CHICLAYO',
        ]);

        Concession::create([
            'id' => 1,
            'concession' => 'Operación y Mantenimiento de las Obras de Irrigación del Proyecto Olmos',
            'registerDate' => '2023-01-01',
            'concessionaire_id' => $concessionaire->id,
            'client_id' => $client->id,
            'created_at' => '2023-01-01',
        ]);
    }
}
