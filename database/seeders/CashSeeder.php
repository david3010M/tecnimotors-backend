<?php

namespace Database\Seeders;

use App\Models\Cash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashSeeder extends Seeder
{
    public function run()
    {
        $cashes = [
            ['series' => '002', 'name' => 'Principal', 'branch_id' => 1],
        ];

        foreach ($cashes as $cash) {
            Cash::create($cash);
        }
    }
}
