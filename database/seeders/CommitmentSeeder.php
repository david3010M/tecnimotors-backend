<?php

namespace Database\Seeders;

use App\Models\budgetSheet;
use App\Models\Commitment;
use Carbon\Carbon;
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
            $quota = $budgetSheet->total / 2;
            for ($i = 1; $i <= 2; $i++) {
                Commitment::factory()->create([
                    'numberQuota' => $i,
                    'price' => $quota,
                    'balance' => $quota,
                    'payment_type' => 'Credito',
                    'payment_date' => Carbon::parse($budgetSheet->attention->arrivalDate)->addDays(7 * $i),
                    'budget_sheet_id' => $budgetSheet->id
                ]);
            }
        });

    }
}
