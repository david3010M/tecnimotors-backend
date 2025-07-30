<?php

namespace App\Services;

use App\Models\budgetSheet;
use App\Models\DetailBudget;

class DetailBudgetService
{
    public function getDetailBudgetById(int $id): ?DetailBudget
    {
        return DetailBudget::find($id);
    }

    public function createDetailBudget(array $data): DetailBudget
    {
        $data['status'] = 'Pendiente'; // Default status
        $detail = DetailBudget::create($data);

        if (isset($data['budget_sheet_id'])) {
            $budget = BudgetSheet::find($data['budget_sheet_id']);
            if ($budget) {
                $this->calculateAndUpdateTotals($budget);
            }
        }

        return $detail;
    }

    public function updateDetailBudget(DetailBudget $instance, array $data): DetailBudget
    {
        $filteredData = array_intersect_key($data, $instance->getAttributes());
        $instance->update($filteredData);

        $budget = $instance->budgetSheet;
        if ($budget) {
            $this->calculateAndUpdateTotals($budget);
        }

        return $instance;
    }

    public function destroyById($id)
    {
        $detail = DetailBudget::find($id);

        if ($detail) {
            $budget = $detail->budgetSheet;
            $detail->delete();

            if ($budget) {
                $this->calculateAndUpdateTotals($budget);
            }

            return true;
        }

        return false;
    }

    public function calculateAndUpdateTotals(BudgetSheet $budget): void
    {
        $budget->load('details');

        $totalProducts = $budget->details
            ->where('type', 'Producto')
            ->sum(fn($detail) => $detail->saleprice * $detail->quantity);

        $totalService = $budget->details
            ->where('type', 'Service')
            ->sum(fn($detail) => $detail->saleprice * $detail->quantity);

        $subtotal = $totalProducts + $totalService;
        $igv = round($subtotal * 0.18, 2);
        $total = $subtotal + $igv;

        $budget->update([
            'totalProducts' => $totalProducts,
            'totalService' => $totalService,
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
            'debtAmount' => $total,
        ]);
    }
}
