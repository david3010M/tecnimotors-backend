<?php

namespace Database\Seeders;

use App\Models\TypeAttention;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeAttentionSeeder extends Seeder
{
    protected $model = TypeAttention::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Reparación'],
            ['name' => 'Mantenimiento'],
            ['name' => 'Revisión'],
            ['name' => 'Diagnóstico']
        ];

        foreach ($array as $object) {
            $typeAttention = TypeAttention::where('name', $object['name'])->first();
            if ($typeAttention) {
                $typeAttention->update($object);
            } else {
                TypeAttention::create($object);
            }
        }
    }
}
