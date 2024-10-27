<?php

namespace Database\Seeders;

use App\Http\Controllers\Controller;
use App\Models\budgetSheet;
use App\Models\Sale;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $budgetSheet = budgetSheet::all();
        Sale::create([
            'number' => '00000001',
            'paymentDate' => Carbon::now()->format('Y-m-d'),
            'documentType' => Constants::SALE_BOLETA,
            'saleType' => Constants::SALE_NORMAL,
            'detractionCode' => null,
            'detractionPercentage' => null,
            'paymentType' => $budgetSheet[0]->paymentType,
            'status' => Constants::SALE_PENDIENTE,
            'taxableOperation' => $budgetSheet[0]->subtotal,
            'igv' => $budgetSheet[0]->igv,
            'total' => $budgetSheet[0]->total,
            'person_id' => 1,
            'budget_sheet_id' => $budgetSheet[0]->id,
            'cash_id' => 1,
        ]);
        $budgetSheet[0]->update(['status' => Constants::BUDGET_SHEET_FACTURADO]);

        Sale::create([
            'number' => '00000001',
            'paymentDate' => Carbon::now()->format('Y-m-d'),
            'documentType' => Constants::SALE_FACTURA,
            'saleType' => Constants::SALE_NORMAL,
            'detractionCode' => null,
            'detractionPercentage' => null,
            'paymentType' => $budgetSheet[1]->paymentType,
            'status' => Constants::SALE_PENDIENTE,
            'taxableOperation' => $budgetSheet[1]->subtotal,
            'igv' => $budgetSheet[1]->igv,
            'total' => $budgetSheet[1]->total,
            'person_id' => 1,
            'budget_sheet_id' => $budgetSheet[1]->id,
            'cash_id' => 1,
        ]);
        $budgetSheet[1]->update(['status' => Constants::BUDGET_SHEET_FACTURADO]);

    }
}
