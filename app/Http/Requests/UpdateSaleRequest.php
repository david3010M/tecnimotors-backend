<?php

namespace App\Http\Requests;

use App\Utils\Constants;
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
            'documentType' => 'required|string|in:' .
                Constants::SALE_BOLETA . ',' .
                Constants::SALE_FACTURA . ',' .
                Constants::SALE_TICKET . ',' .
                Constants::SALE_NOTA_CREDITO_BOLETA . ',' .
                Constants::SALE_NOTA_CREDITO_FACTURA . "'", // BOLETA, FACTURA, TICKET, NOTA_CREDITO_BOLETA, NOTA_CREDITO_FACTURA
            'saleType' => 'required|string|in:' .
                Constants::SALE_NORMAL . ',' .
                Constants::SALE_DETRACCION . "'", // NORMAL, ANTICIPO, DETRACCION
            'detractionCode' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|string',
            'detractionPercentage' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|numeric',
            'paymentType' => 'required|string|in:' .
                Constants::SALE_CONTADO . ',' .
                Constants::SALE_CREDITO . "'", // CONTADO, CREDITO
            'person_id' => 'required|integer|exists:people,id',
            'budget_sheet_id' => [
                'required',
                'integer',
                'exists:budget_sheets,id',
                Rule::unique('sales', 'budget_sheet_id')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('sale'))
            ],
            'saleDetails' => 'required|array',
            'saleDetails.*.description' => 'required|string',
            'saleDetails.*.unit' => 'required|string',
            'saleDetails.*.quantity' => 'required|numeric',
            'saleDetails.*.unitValue' => 'required|numeric',
            'saleDetails.*.unitPrice' => 'required|numeric',
            'saleDetails.*.discount' => 'nullable|numeric',
            'saleDetails.*.subTotal' => 'required|numeric',
        ];
    }
}
