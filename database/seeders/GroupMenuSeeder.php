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
            ['id' => '1', 'name' => 'Principal', 'icon' => 'fa-solid fa-house'],
            ['id' => '2', 'name' => 'Bases', 'icon' => 'fa-solid fa-building'],
            ['id' => '3', 'name' => 'Personas', 'icon' => 'fa-solid fa-user'],
            ['id' => '4', 'name' => 'Vehiculos', 'icon' => 'fa-solid fa-house'],
            ['id' => '5', 'name' => 'Repuestos', 'icon' => 'fa-solid fa-screwdriver-wrench'],
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
