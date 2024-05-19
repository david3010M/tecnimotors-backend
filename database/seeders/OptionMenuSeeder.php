<?php

namespace Database\Seeders;

use App\Models\Optionmenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['id' => '1', 'name' => 'Option 1', 'route' => 'option1', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 1],
            ['id' => '2', 'name' => 'Option 2', 'route' => 'option2', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 2],
            ['id' => '3', 'name' => 'Option 3', 'route' => 'option3', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 3],
            ['id' => '4', 'name' => 'Option 4', 'route' => 'option4', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 4],
            ['id' => '5', 'name' => 'Option 5', 'route' => 'option5', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 5],
        ];

        foreach ($array as $object) {
            $typeOfuser1 = Optionmenu::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                Optionmenu::create($object);
            }
        }
    }
}
