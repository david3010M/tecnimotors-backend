<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['id' => '1', 'typeofDocument' => 'DNI',
                'documentNumber' => '12345678', 'names' => 'admin',
                'fatherSurname' => 'apellido P', 'motherSurname' =>
                'apeliddo M'],
            ['id' => '2', 'typeofDocument' => 'DNI',
                'documentNumber' => '72345678', 'names' => 'varios',
                'fatherSurname' => '-', 'motherSurname' =>
                '-'],

            ['id' => '3', 'typeofDocument' => 'RUC',
                'documentNumber' => '10123456789',
                'businessName' => 'company SAC'],
        ];

        foreach ($array as $object) {
            $typeOfuser1 = Person::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                Person::create($object);
            }
        }
    }
}
