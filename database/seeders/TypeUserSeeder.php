<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Seeder;

class TypeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['id' => '1', 'name' => 'Administrador'],
            ['id' => '2', 'name' => 'PruebaDemo'],
            ['id' => '3', 'name' => 'Visitante'],
            ['id' => '4', 'name' => 'Worker'],
        ];

        foreach ($array as $object) {
            $typeOfuser1 = TypeUser::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                TypeUser::create($object);
            }
        }

    }
}
