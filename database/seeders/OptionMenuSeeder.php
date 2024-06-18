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
            ['id' => '1', 'name' => 'Hoja de Servicio', 'route' => 'hojaServicio', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 1],
            ['id' => '2', 'name' => 'Presupuesto', 'route' => 'presupuesto', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 1],
            ['id' => '3', 'name' => 'Servicio', 'route' => 'servicio', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 2],
            ['id' => '4', 'name' => 'Almacen', 'route' => 'almacen', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 1],
            ['id' => '5', 'name' => 'Concepto', 'route' => 'concepto', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 2],
            ['id' => '6', 'name' => 'Compromiso', 'route' => 'compromiso', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 2],
            ['id' => '7', 'name' => 'Caja', 'route' => 'caja', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 1],
            ['id' => '8', 'name' => 'Seguimiento', 'route' => 'seguimiento', 'icon' => 'fa-solid fa-house', 'groupmenu_id' => 3],

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
