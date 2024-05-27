<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    protected $model = Bank::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Bank 1'],
            ['name' => 'Bank 2'],
            ['name' => 'Bank 3'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
