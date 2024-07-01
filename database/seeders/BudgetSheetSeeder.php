<?php

namespace Database\Seeders;

use App\Models\Attention;
use App\Models\budgetSheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attentions = Attention::all();

        foreach ($attentions as $attention) {
            $total = $attention->totalService + $attention->totalProducts;
            $debtAmount = $total * 0.5;
            budgetSheet::factory()->create([
                'totalService' => $attention->totalService,
                'totalProducts' => $attention->totalProducts,
                'total' => $total,
                'debtAmount' => $debtAmount,
                'attention_id' => $attention->id,
            ]);
        }

    }
}
