<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    protected $model = Supplier::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['date' => '2024-05-22', 'category' => 'Category 1', 'person_id' => 3],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
