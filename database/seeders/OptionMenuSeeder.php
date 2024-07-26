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
            ['id' => '1', 'name' => 'Hoja de Servicio', 'route' => 'hojaservicio', 'groupmenu_id' => 1],
            ['id' => '2', 'name' => 'Presupuesto', 'route' => 'presupuesto', 'groupmenu_id' => 1],
            ['id' => '3', 'name' => 'Servicios', 'route' => 'listService', 'groupmenu_id' => 2],
            ['id' => '4', 'name' => 'Almacen', 'route' => 'listAlmacen', 'groupmenu_id' => 2],
            ['id' => '5', 'name' => 'Servicios Pendientes', 'route' => 'listServicesTrabajadores', 'groupmenu_id' => 3],
            ['id' => '6', 'name' => 'Caja', 'route' => 'caja', 'groupmenu_id' => 4],
            ['id' => '7', 'name' => 'Concepto', 'route' => 'listConcept', 'groupmenu_id' => 4],
            ['id' => '8', 'name' => 'Compromiso', 'route' => 'listCommitment', 'groupmenu_id' => 4],

            ['id' => '9', 'name' => 'Gestionar Accesos', 'route' => 'accesos', 'groupmenu_id' => 5],
            ['id' => '10', 'name' => 'Personas', 'route' => 'persona', 'groupmenu_id' => 5],
            ['id' => '11', 'name' => 'Usuarios', 'route' => 'usuarios', 'groupmenu_id' => 5],
            ['id' => '12', 'name' => 'Trabajadores', 'route' => 'trabajador', 'groupmenu_id' => 5],

            ['id' => '13', 'name' => 'Categorias', 'route' => 'categorias', 'groupmenu_id' => 2],
            ['id' => '14', 'name' => 'Marcas', 'route' => 'marcas', 'groupmenu_id' => 2],
            ['id' => '15', 'name' => 'Unidades', 'route' => 'unidades', 'groupmenu_id' => 2],

            ['id' => '16', 'name' => 'Vehiculos', 'route' => 'vehiculos', 'groupmenu_id' => 2],

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
