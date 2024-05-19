<?php

namespace Database\Seeders;

use App\Models\GroupMenu;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['id' => '1', 'name' => 'Grupo 1', 'icon' => 'fa-solid fa-house'],
            ['id' => '2', 'name' => 'Grupo 2', 'icon' => 'fa-solid fa-house'],
            ['id' => '3', 'name' => 'Grupo 3', 'icon' => 'fa-solid fa-house'],
            ['id' => '4', 'name' => 'Grupo 4', 'icon' => 'fa-solid fa-house'],
            ['id' => '5', 'name' => 'Grupo 5', 'icon' => 'fa-solid fa-house'],
        ];

        foreach ($array as $object) {
            $typeOfuser1 = GroupMenu::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                GroupMenu::create($object);
            }
        }
    }
}
