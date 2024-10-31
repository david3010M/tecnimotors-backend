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
            ['id' => '1', 'typeofDocument' => 'DNI', 'documentNumber' => '11111111',
                'names' => 'Administrador', 'fatherSurname' => '-', 'motherSurname' => '-',
                'address' => '123 Main St', 'phone' => '903017426',
                'email' => 'johndoe@gmail.com', 'origin' => 'Lambayeque', 'ocupation' => 'Administrador'],

            ['id' => '2', 'typeofDocument' => 'DNI', 'documentNumber' => '00000000',
                'names' => 'VARIOS', 'fatherSurname' => '-', 'motherSurname' => '-',
                'address' => '123 Main St', 'phone' => '903017426',
                'email' => 'johndoe@gmail.com', 'origin' => 'Lambayeque', 'ocupation' => 'VARIOS'],

            ['id' => '3', 'typeofDocument' => 'RUC', 'documentNumber' => '20600417461',
                'businessName' => 'INVERSIONES LACTEAS DEL NORTE S.A.C.', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '903017426',
                'email' => 'inversionelacteas@gmail.com', 'origin' => 'City', 'ocupation' => 'Proveedor'],

            ['id' => '4', 'typeofDocument' => 'DNI', 'documentNumber' => '12345671', 'names' => 'Juan',
                'fatherSurname' => 'Pérez', 'motherSurname' => 'González', 'address' => '456 Elm St',
                'phone' => '987654321', 'email' => 'juanperez@example.com', 'origin' => 'Lima', 'ocupation' => 'Mecanico'],

            ['id' => '5', 'typeofDocument' => 'DNI', 'documentNumber' => '23456781', 'names' => 'Carlos',
                'fatherSurname' => 'Ramírez', 'motherSurname' => 'Fernández', 'address' => '123 Main St',
                'phone' => '903017426', 'email' => 'carlosramirez@example.com', 'origin' => 'Trujillo', 'ocupation' => 'Mecanico'],

            ['id' => '6', 'typeofDocument' => 'DNI', 'documentNumber' => '34567891', 'names' => 'Luis',
                'fatherSurname' => 'Hernández', 'motherSurname' => 'García', 'address' => '654 Maple St',
                'phone' => '987321654', 'email' => 'luishernandez@example.com', 'origin' => 'Cusco', 'ocupation' => 'Mecanico'],

            ['id' => '7', 'typeofDocument' => 'DNI', 'documentNumber' => '45678901', 'names' => 'Ana',
                'fatherSurname' => 'Díaz', 'motherSurname' => 'Torres', 'address' => '321 Oak St',
                'phone' => '876543210', 'email' => 'anadiaz@example.com', 'origin' => 'Chiclayo', 'ocupation' => 'Asesor'],

            ['id' => '8', 'typeofDocument' => 'DNI', 'documentNumber' => '56789022', 'names' => 'Miguel',
                'fatherSurname' => 'Morales', 'motherSurname' => 'Rojas', 'address' => '123 Main St',
                'phone' => '903017426', 'email' => 'miguelmorales@example.com', 'origin' => 'Lambayeque', 'ocupation' => 'Asesor'],
        ];

        foreach ($array as $object) {
            $typeOfuser1 = Person::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                Person::create($object);
            }
        }
        $this->call(FuncitionFormaPagoSeeder::class);

        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);

        $this->call(OptionMenuSeeder::class);
        $this->call(OcupationSeeder::class);
        $this->call(WorkerSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TypeAttentionSeeder::class);
        $this->call(TypeVehicleSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(ElementSeeder::class);
        $this->call(VehicleModelSeeder::class);
        $this->call(VehicleSeeder::class);

        $this->call(UnitSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ConceptMovSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(ConceptPaySeeder::class);

        $this->call(SpecialtySeeder::class);
        $this->call(SpecialtyByPersonSeeder::class);
        $this->call(ServiceSeeder::class);

        $this->call(AttentionSeeder::class);
        $this->call(ElementForAttentionSeeder::class);
        $this->call(DetailAttentionSeeder::class);
        $this->call(budgetSheetSeeder::class);
        $this->call(TaskSeeder::class);

        $this->call(CommitmentSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(CashSeeder::class);
        $this->call(SaleSeeder::class);

    }
}
