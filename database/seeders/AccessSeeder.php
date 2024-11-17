<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeUser::find(1)->setAccess(1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,22]);//administrador backend
        TypeUser::find(2)->setAccess(2, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,22]);//administrador
        TypeUser::find(3)->setAccess(3, [1, 2, 6, 7, 8, 10]); //cajero
        TypeUser::find(4)->setAccess(4, [1, 2, 5, 10]); //mecanico
        TypeUser::find(5)->setAccess(5, [1, 2, 3, 4]);//asesor
    }
}
