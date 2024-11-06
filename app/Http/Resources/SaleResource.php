<?php

namespace App\Http\Resources;

use App\Utils\Constants;
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
 *     @OA\Property(property="taxableOperation", type="number", example="100.00"),
 *     @OA\Property(property="igv", type="number", example="18.00"),
 *     @OA\Property(property="total", type="number", example="118.00"),
 *     @OA\Property(property="yape", type="number", example="10.00"),
 *     @OA\Property(property="deposit", type="number", example="10.00"),
 *     @OA\Property(property="effective", type="number", example="10.00"),
 *     @OA\Property(property="card", type="number", example="10.00"),
 *     @OA\Property(property="plin", type="number", example="10.00"),
 *     @OA\Property(property="isBankPayment", type="boolean", example="false"),
 *     @OA\Property(property="bank_id", type="integer", example="1"),
 *     @OA\Property(property="bank", type="string", example="BCP"),
 *     @OA\Property(property="numberVoucher", type="string", example="123456"),
 *     @OA\Property(property="routeVoucher", type="string", example="http://example.com"),
 *     @OA\Property(property="comment", type="string", example="comment"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1"),
 *     @OA\Property(property="cash_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00"),
 * )
 *
 * @OA\Schema (
 *     schema="SaleSingleResource",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SaleResource"),
 *         @OA\Schema(
 *             @OA\Property(property="budgetSheet", type="object", ref="#/components/schemas/BudgetSheetSingle"),
 *             @OA\Property(property="saleDetails", type="array", @OA\Items(ref="#/components/schemas/SaleDetailResource")),
 *             @OA\Property(property="commitments", type="array", @OA\Items(ref="#/components/schemas/CommitmentResource")),
 *             @OA\Property(property="cash", type="object", ref="#/components/schemas/Cash"),
 *         )
 *     }
 * )
 *
 *
 * @OA\Schema (
 *     schema="SaleCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SaleResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
 * )
 */
class SaleResource extends JsonResource
{
    protected bool $includeBudgetSheet = false;

    public function toArray($request): array
    {
        $data = [
            'number' => $this->fullNumber,
            'paymentDate' => $this->paymentDate ? $this->paymentDate->format('Y-m-d') : null,
            'documentType' => $this->documentType,
            'saleType' => $this->saleType,
            'detractionCode' => $this->detractionCode,
            'detractionPercentage' => $this->detractionPercentage,
            'paymentType' => $this->paymentType,
            'status' => $this->status,
            'taxableOperation' => $this->taxableOperation,
            'igv' => $this->igv,
            'total' => $this->total,
            'yape' => $this->yape,
            'deposit' => $this->deposit,
            'effective' => $this->effective,
            'card' => $this->card,
            'plin' => $this->plin,
            'isBankPayment' => $this->isBankPayment,
            'bank' => $this->bank?->name,
            'bank_id' => $this->bank_id,
            'numberVoucher' => $this->numberVoucher,
            'routeVoucher' => $this->routeVoucher,
            'comment' => $this->comment,
            'person_id' => $this->person_id,
            'budget_sheet_id' => $this->budget_sheet_id,
            'cash_id' => $this->cash_id,
            'created_at' => $this->created_at,
        ];

        if ($this->includeBudgetSheet) {
            $data['saleDetails'] = SaleDetailResource::collection($this->saleDetails);
            $data['commitments'] = CommitmentResource::collection($this->commitments);
            $data['budgetSheet'] = $this->budgetSheet;
            $data['cash'] = $this->cash;
        }

        return $data;
    }

    public function withBudgetSheet(): self
    {
        $this->includeBudgetSheet = true;
        return $this;
    }
}
