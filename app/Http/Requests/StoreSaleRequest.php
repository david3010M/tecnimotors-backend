<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="StoreSaleRequest",
 *     title="StoreSaleRequest",
 *     required={"paymentDate", "documentType", "saleType", "paymentType", "person_id", "budget_sheet_id"},
 *     @OA\Property(property="paymentDate", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="documentType", type="string", example="FACTURA"),
 *     @OA\Property(property="saleType", type="string", example="NORMAL"),
 *     @OA\Property(property="detractionCode", type="string", example="123456"),
 *     @OA\Property(property="detractionPercentage", type="string", example="10.00"),
 *     @OA\Property(property="paymentType", type="string", example="CONTADO"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1")
 * )
 */
class StoreSaleRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'paymentDate' => 'required|date_format:Y-m-d',
            'documentType' => 'required|string|in:BOLETA,FACTURA,TICKET',
            'saleType' => 'required|string|in:NORMAL,DETRACCION',
            'detractionCode' => 'nullable|required_if:saleType,DETRACCION|string',
            'detractionPercentage' => 'nullable|required_if:saleType,DETRACCION|string',
            'paymentType' => 'required|string|in:CONTADO,CREDITO',
            'person_id' => 'required|integer|exists:people,id',
            'budget_sheet_id' => [
                'required',
                'integer',
                'exists:budget_sheets,id',
                Rule::unique('sales', 'budget_sheet_id')
                    ->whereNull('deleted_at')
            ]
        ];
    }
}
