<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    protected $model = Category::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Category 1'],
            ['name' => 'Category 2'],
            ['name' => 'Category 3']
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
