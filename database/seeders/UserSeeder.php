<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['id' => '1', 'username' => 'admin', 'password' => 'adminTecnimotors', 'worker_id' => '1', 'typeofUser_id' => '1'],
            ['id' => '2', 'username' => 'adminBack', 'password' => 'adminBack123', 'worker_id' => '1', 'typeofUser_id' => '2'],
            ['id' => '3', 'username' => 'JuanPerez', 'password' => 'JuanPerez', 'worker_id' => '3', 'typeofUser_id' => '4'],
            ['id' => '4', 'username' => 'CarlosRamirez', 'password' => 'CarlosRamirez', 'worker_id' => '4', 'typeofUser_id' => '3'],
            ['id' => '5', 'username' => 'LuisHernandez', 'password' => 'LuisHernandez', 'worker_id' => '5', 'typeofUser_id' => '4'],
            ['id' => '6', 'username' => 'JoseFernandez', 'password' => 'JoseFernandez', 'worker_id' => '6', 'typeofUser_id' => '4'],
            ['id' => '7', 'username' => 'MartinMorante', 'password' => 'MartinMorante', 'worker_id' => '7', 'typeofUser_id' => '5'],
        ];

        foreach ($users as $user) {
            // Buscar el registro por su ID
            $user1 = User::find($user['id']);

            // Hashear la contraseña
            $user['password'] = Hash::make($user['password']);

            // Si el usuario existe, actualizarlo; de lo contrario, crear uno nuevo
            if ($user1) {
                $user1->update($user);
            } else {
                User::create($user);
            }
        }
    }
}
