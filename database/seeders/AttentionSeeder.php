<?php

namespace Database\Seeders;

use App\Models\Attention;
use Illuminate\Database\Seeder;

class AttentionSeeder extends Seeder
{
    protected $model = Attention::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attention::factory()->count(10)->create();
    }
}
