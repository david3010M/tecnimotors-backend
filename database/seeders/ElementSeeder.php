<?php

namespace Database\Seeders;

use App\Models\Element;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    protected $model = Element::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Manual de Serv.'],
            ['name' => 'Rueda de repuesto'],
            ['name' => 'Gata'],
            ['name' => 'Herramientas'],
            ['name' => 'Triángulo'],
            ['name' => 'Extintor'],
            ['name' => 'Lunas'],
            ['name' => 'Faros'],
            ['name' => 'Vasos'],
            ['name' => 'Ruedas'],
            ['name' => 'Asientos'],
            ['name' => 'Bocinas'],
            ['name' => 'Encendedor'],
            ['name' => 'Emblemas'],
            ['name' => 'Tapa gasolina'],
            ['name' => 'Seguros de rueda'],
            ['name' => 'AC o ventilador'],
            ['name' => 'Carátula radio'],
            ['name' => 'Radiocasete'],
            ['name' => 'Sun rool'],
            ['name' => 'Alfombras'],
            ['name' => 'Espejos laterales'],
            ['name' => 'Plumillos'],
            ['name' => 'Plumilla post'],
            ['name' => 'Botiquín'],
            ['name' => 'Cambiador CD'],
            ['name' => 'Discos'],
            ['name' => 'Control'],
            ['name' => 'Control de prueba'],
            ['name' => 'Antena'],
            ['name' => 'Tarjeta de propiedad'],
            ['name' => 'SOAT']
        ];
        

        foreach ($array as $item) {
            Element::create($item);
        }
    }
}
