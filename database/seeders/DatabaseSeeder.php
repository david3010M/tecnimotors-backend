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
        /**
         * typeofDocument
         * documentNumber
         * names
         * fatherSurname
         * motherSurname
         * businessName
         * representativeDni
         * representativeNames
         * address
         * phone
         * email
         * origin
         * ocupation
         */
        $array = [
            ['id' => '1', 'typeofDocument' => 'DNI', 'documentNumber' => '31648134', 'names' => 'Miguel Angel', 'fatherSurname' => 'Guevara',
                'motherSurname' => 'Cajusol', 'businessName' => 'Doe Enterprises', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'johndoe@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer'],
            ['id' => '2', 'typeofDocument' => 'DNI', 'documentNumber' => '16456616', 'names' => 'Jane', 'fatherSurname' => 'Doe',
                'motherSurname' => 'Smith', 'businessName' => 'Doe Enterprises', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'janedoe@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer'],
            ['id' => '3', 'typeofDocument' => 'DNI', 'documentNumber' => '94314462', 'names' => 'Tyler', 'fatherSurname' => 'Doe',
                'motherSurname' => 'Smith', 'businessName' => 'Doe Enterprises', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'tyler@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer'],
            ['id' => '4', 'typeofDocument' => 'DNI', 'documentNumber' => '64134613', 'names' => 'James', 'fatherSurname' => 'Doe',
                'motherSurname' => 'Smith', 'businessName' => 'Doe Enterprises', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'james@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer'],
            ['id' => '5', 'typeofDocument' => 'DNI', 'documentNumber' => '54781645', 'names' => 'George', 'fatherSurname' => 'Doe',
                'motherSurname' => 'Smith', 'businessName' => 'Doe Enterprises', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'geoorge@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer'],
            ['id' => '6', 'typeofDocument' => 'RUC', 'documentNumber' => '20600417461', 'names' => 'Michael', 'fatherSurname' => 'Doe',
                'motherSurname' => 'Smith', 'businessName' => 'INVERSIONES LACTEAS DEL NORTE S.A.C.', 'representativeDni' => '87654321',
                'representativeNames' => 'Jane Doe', 'address' => '123 Main St', 'phone' => '+1234567890',
                'email' => 'inversionelacteas@gmail.com', 'origin' => 'City', 'ocupation' => 'Engineer']
        ];

        foreach ($array as $object) {
            $typeOfuser1 = Person::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                Person::create($object);
            }
        }

        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);

        $this->call(OptionMenuSeeder::class);
        $this->call(WorkerSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TypeAttentionSeeder::class);
        $this->call(TypeVehicleSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(ElementSeeder::class);
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
    }
}
