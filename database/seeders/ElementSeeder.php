<?php

namespace Database\Seeders;

use App\Models\Element;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    protected $model = Element::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Element 1'],
            ['name' => 'Element 2'],
            ['name' => 'Element 3'],
            ['name' => 'Element 4'],
            ['name' => 'Element 5'],
            ['name' => 'Element 6'],
            ['name' => 'Element 7'],
            ['name' => 'Element 8'],
            ['name' => 'Element 9'],
            ['name' => 'Element 10']
        ];

        foreach ($array as $item) {
            Element::create($item);
        }
    }
}
