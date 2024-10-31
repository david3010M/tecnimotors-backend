<?php

namespace Database\Seeders;

use App\Models\Attention;
use App\Models\budgetSheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class budgetSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attentions = Attention::all();

        // foreach ($attentions as $attention) {
        //     $subtotal = $attention->total;
        //     $total = ($subtotal * 1.18);
        //     $debtAmount = $total * 0.5;
        //     budgetSheet::factory()->create([
        //         'totalService' => $attention->totalService,
        //         'totalProducts' => $attention->totalProducts,
        //         'total' => $total,
        //         'discount' => 0,
        //         'debtAmount' => $debtAmount,
        //         'attention_id' => $attention->id,
        //     ]);

        //     $attention->debtAmount = $debtAmount;
        //     $attention->save();
        // }

    }
}
