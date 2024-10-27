<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="SaleResource",
 *     @OA\Property(property="number", type="string", example="VENT-123456"),
 *     @OA\Property(property="paymentDate", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="documentType", type="string", example="FACTURA"),
 *     @OA\Property(property="saleType", type="string", example="NORMAL"),
 *     @OA\Property(property="detractionCode", type="string", example="123456"),
 *     @OA\Property(property="detractionPercentage", type="string", example="10.00"),
 *     @OA\Property(property="paymentType", type="string", example="CONTADO"),
 *     @OA\Property(property="status", type="string", example="PENDING"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00"),
 * )
 *
 * @OA\Schema (
 *     schema="SaleSingleResource",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SaleResource"),
 *         @OA\Schema(
 *             @OA\Property(property="budgetSheet", type="object", ref="#/components/schemas/BudgetSheetSingle")
 *         )
 *     }
 * )
 *
 *
 * @OA\Schema (
 *     schema="SaleCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SaleResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class SaleResource extends JsonResource
{
    protected bool $includeBudgetSheet = false;

    public function toArray($request): array
    {
        $data = [
            'number' => 'VENT-' . $this->number,
            'paymentDate' => $this->paymentDate ? $this->paymentDate->format('Y-m-d') : null,
            'documentType' => $this->documentType,
            'saleType' => $this->saleType,
            'detractionCode' => $this->detractionCode,
            'detractionPercentage' => $this->detractionPercentage,
            'paymentType' => $this->paymentType,
            'status' => $this->status,
            'total' => $this->total,
            'person_id' => $this->person_id,
            'budget_sheet_id' => $this->budget_sheet_id,
            'created_at' => $this->created_at,
        ];

        if ($this->includeBudgetSheet) {
            $data['budgetSheet'] = $this->budgetSheet;
        }

        return $data;
    }

    public function withBudgetSheet(): self
    {
        $this->includeBudgetSheet = true;
        return $this;
    }
}
