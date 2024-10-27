<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="UpdateSaleRequest",
 *     title="UpdateSaleRequest",
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
class UpdateSaleRequest extends UpdateRequest
{
    public function rules()
    {
        return [
            'paymentDate' => 'nullable|date_format:Y-m-d',
            'documentType' => 'nullable|string|in:BOLETA,FACTURA,TICKET',
            'saleType' => 'nullable|string|in:NORMAL,DETRACCION',
            'detractionCode' => 'nullable|string',
            'detractionPercentage' => 'nullable|string',
            'paymentType' => 'nullable|string|in:CONTADO,CREDITO',
            'person_id' => 'nullable|integer|exists:people,id',
            'budget_sheet_id' => [
                'required',
                'integer',
                'exists:budget_sheets,id',
                Rule::unique('sales', 'budget_sheet_id')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('sale'))
            ]
        ];
    }
}
