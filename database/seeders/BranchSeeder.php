<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branches = [
            ['name' => 'Tecnimotors del Perú'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
