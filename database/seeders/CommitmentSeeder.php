<?php

namespace Database\Seeders;

use App\Models\budgetSheet;
use App\Models\Commitment;
use App\Models\Sale;
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
        $sales = Sale::all();
        $sales->each(function ($sale) {
            $quota = $sale->total / 2;
            for ($i = 1; $i <= 2; $i++) {
                Commitment::factory()->create([
                    'numberQuota' => $i,
                    'price' => $quota,
                    'balance' => $quota,
                    'payment_type' => 'Credito',
                    'payment_date' => Carbon::parse($sale->budgetSheet->attention->arrivalDate)->addDays(7 * $i),
                    'sale_id' => $sale->id
                ]);
            }
        });

    }
}
