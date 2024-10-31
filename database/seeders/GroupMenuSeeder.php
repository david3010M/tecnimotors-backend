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
            ['id' => '1', 'name' => 'Principal', 'icon' => 'HomeIcon'],
            ['id' => '2', 'name' => 'Servicios', 'icon' => 'BuildIcon'],
            ['id' => '3', 'name' => 'Trabajador', 'icon' => 'UserIcon'],
            ['id' => '4', 'name' => 'Facturación', 'icon' => 'VanIcon'],
            ['id' => '5', 'name' => 'Administración', 'icon' => 'ScrewIcon'],
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
