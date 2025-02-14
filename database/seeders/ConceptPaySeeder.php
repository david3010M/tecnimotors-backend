<?php

namespace Database\Seeders;

use App\Models\ConceptPay;
use Illuminate\Database\Seeder;

class ConceptPaySeeder extends Seeder
{
    protected $model = ConceptPay::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['number' => 'CONC-00000001', 'name' => 'Apertura', 'type' => 'Ingreso'],
            ['number' => 'CONC-00000002', 'name' => 'Cierre', 'type' => 'Egreso'],

            ['number' => 'CONC-00000003', 'name' => 'Pago de proveedores', 'type' => 'Egreso'],
            ['number' => 'CONC-00000004', 'name' => 'Ingreso por servicios adicionales', 'type' => 'Ingreso'],
            ['number' => 'CONC-00000005', 'name' => 'Retiro de efectivo para gastos menores', 'type' => 'Egreso'],
            ['number' => 'CONC-00000007', 'name' => 'Pago de salario de empleados', 'type' => 'Egreso'],
            ['number' => 'CONC-00000008', 'name' => 'Amortización de compromisos', 'type' => 'Ingreso'],
            ['number' => 'CONC-00000009', 'name' => 'Nota de Crédito', 'type' => 'Ingreso']


        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
