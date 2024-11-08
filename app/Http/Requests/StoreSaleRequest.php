<?php

namespace App\Http\Requests;

use App\Utils\Constants;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="StoreSaleRequest",
 *     title="StoreSaleRequest",
 *     required={"paymentDate", "documentType", "saleType", "paymentType", "person_id", "budget_sheet_id"},
 *     @OA\Property(property="paymentDate", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="documentType", type="string", example="FACTURA", enum={"BOLETA", "FACTURA", "TICKET", "NOTA_CREDITO_BOLETA", "NOTA_CREDITO_FACTURA"}),
 *     @OA\Property(property="saleType", type="string", example="NORMAL", enum={"NORMAL", "DETRACCION"}),
 *     @OA\Property(property="detractionCode", type="string", example="123456"),
 *     @OA\Property(property="detractionPercentage", type="string", example="10.00"),
 *     @OA\Property(property="paymentType", type="string", example="CONTADO", enum={"CONTADO", "CREDITO"}),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1"),
 *     @OA\Property(property="yape", type="number", example="10.00"),
 *     @OA\Property(property="deposit", type="number", example="10.00"),
 *     @OA\Property(property="effective", type="number", example="10.00"),
 *     @OA\Property(property="card", type="number", example="10.00"),
 *     @OA\Property(property="plin", type="number", example="10.00"),
 *     @OA\Property(property="isBankPayment", type="number", example="0", enum={0, 1}),
 *     @OA\Property(property="bank_id", type="integer", example="1"),
 *     @OA\Property(property="routeVoucher", type="file", format="binary"),
 *     @OA\Property(property="comment", type="string", example="comment"),
 *     @OA\Property(property="saleDetails[]", type="array", @OA\Items(
 *         @OA\Property(property="description", type="string", example="Producto 1"),
 *         @OA\Property(property="unit", type="string", example="UNIDAD"),
 *         @OA\Property(property="quantity", type="number", example="10"),
 *         @OA\Property(property="unitValue", type="number", example="100"),
 *         @OA\Property(property="unitPrice", type="number", example="118"),
 *         @OA\Property(property="discount", type="number", example="0"),
 *         @OA\Property(property="subTotal", type="number", example="100"),
 *     )),
 *     @OA\Property(property="commitments[]", type="array", @OA\Items(
 *         @OA\Property(property="price", type="number", example="100.00"),
 *         @OA\Property(property="paymentDate", type="integer", example="1"),
 *     )),
 * )
 */
class StoreSaleRequest extends StoreRequest
{
    public function rules()
    {
        $data = [
            'paymentDate' => 'required',
            'documentType' => 'required|in:' .
                Constants::SALE_BOLETA . ',' .
                Constants::SALE_FACTURA . ',' .
                Constants::SALE_TICKET . ',' .
                Constants::SALE_NOTA_CREDITO_BOLETA . ',' .
                Constants::SALE_NOTA_CREDITO_FACTURA, // BOLETA, FACTURA, TICKET, NOTA_CREDITO_BOLETA, NOTA_CREDITO_FACTURA
            'saleType' => 'required|string|in:' .
                Constants::SALE_NORMAL . ',' .
                Constants::SALE_DETRACCION, // NORMAL, ANTICIPO, DETRACCION
            'detractionCode' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|string',
            'detractionPercentage' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|numeric',
            'paymentType' => 'required|string|in:' .
                Constants::SALE_CONTADO . ',' .
                Constants::SALE_CREDITO, // CONTADO, CREDITO
            'person_id' => 'required|integer|exists:people,id',
            'budget_sheet_id' => [
                'nullable',
                'integer',
                'exists:budget_sheets,id',
            ],
            'yape' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'effective' => 'nullable|numeric',
            'card' => 'nullable|numeric',
            'plin' => 'nullable|numeric',
            'isBankPayment' => 'required|in:0,1',
            'nro_operation' => 'nullable|string',
            'bank_id' => 'required_if:isBankPayment,1|integer|exists:banks,id',
            'routeVoucher' => 'nullable|file',
            'comment' => 'nullable|string',
            'saleDetails' => 'required|array',
            'saleDetails.*.description' => 'required|string',
            'saleDetails.*.unit' => 'required|string',
            'saleDetails.*.quantity' => 'required|numeric',
            'saleDetails.*.unitValue' => 'required|numeric',
            'saleDetails.*.unitPrice' => 'required|numeric',
            'saleDetails.*.discount' => 'nullable|numeric',
            'saleDetails.*.subTotal' => 'required|numeric',
            'commitments' => 'required_if:paymentType,' . Constants::SALE_CREDITO . '|array',
            'commitments.*.price' => 'required|numeric',
            'commitments.*.paymentDate' => 'required|int',
        ];
        return $data;
    }
}
