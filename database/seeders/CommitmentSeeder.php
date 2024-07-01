<?php

namespace Database\Seeders;

use App\Models\budgetSheet;
use App\Models\Commitment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommitmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $budgetSheets = budgetSheet::all();
        $budgetSheets->each(function ($budgetSheet) {
            $totalPrice = $budgetSheet->total;
            $initialPayment = $budgetSheet->debtAmount;
            Commitment::factory()->create([
                'dues' => 2,
                'amount' => $initialPayment,
                'balance' => $totalPrice - $initialPayment,
                'budget_sheet_id' => $budgetSheet->id
            ]);
        });

    }
}
