<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailBudgetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('detail_budgets')->insert([
            [
                'saleprice' => 'Sample',
            'quantity' => 'Sample',
            'type' => 'Example type',
            'comment' => 'Sample',
            'status' => 'Sample',
            'dateRegister' => 'Sample',
            'dateMax' => 'Sample',
            'dateCurrent' => 'Sample',
            'percentage' => 'Sample',
            'period' => 'Sample',
            'attention_id' => 'Sample',
            'worker_id' => 'Sample',
            'service_id' => 'Sample',
            'product_id' => 'Sample',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}