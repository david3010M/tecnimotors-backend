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

            'saleType' => 'required|string|in:' .
                Constants::SALE_NORMAL . ',' .
                Constants::SALE_DETRACCION. ',' . // NORMAL, ANTICIPO, DETRACCION
            Constants::SALE_RETENCION,
            'retencion' => 'nullable|numeric|min:0|max:100|required_if:saleType,' . Constants::SALE_RETENCION . '|string',
            'detractionCode' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|string',
            'detractionPercentage' => 'nullable|min:0|max:100|required_if:saleType,' . Constants::SALE_DETRACCION . '|numeric',





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

    public function messages()
{
    return [
        // Genéricos
        'required' => 'El campo :attribute es obligatorio.',
        'numeric'  => 'El campo :attribute debe ser numérico.',
        'integer'  => 'El campo :attribute debe ser un número entero.',
        'in'       => 'El campo :attribute no es válido.',
        'exists'   => 'El :attribute seleccionado no existe.',
        'file'     => 'El campo :attribute debe ser un archivo.',

        // Campos principales
        'paymentDate.required'    => 'Debes indicar la fecha de pago.',
        'documentType.required'   => 'Debes seleccionar el tipo de documento.',
        'documentType.in'         => 'El tipo de documento no es válido.',
        'saleType.required'       => 'Debes indicar el tipo de venta.',
        'saleType.in'             => 'El tipo de venta no es válido.',
        'paymentType.required'    => 'Debes indicar el tipo de pago.',
        'paymentType.in'          => 'El tipo de pago no es válido.',
        'person_id.required'      => 'Debes seleccionar un cliente.',
        'person_id.integer'       => 'El cliente es inválido.',
        'person_id.exists'        => 'El cliente seleccionado no existe.',
        'budget_sheet_id.integer' => 'El presupuesto es inválido.',
        'budget_sheet_id.exists'  => 'El presupuesto seleccionado no existe.',

        // Bancos y pagos
        'isBankPayment.required'  => 'Debes indicar si el pago es bancario.',
        'isBankPayment.in'        => 'El indicador de pago bancario no es válido.',
        'bank_id.required_if'     => 'Debes seleccionar un banco cuando el pago es bancario.',
        'bank_id.integer'         => 'El banco es inválido.',
        'bank_id.exists'          => 'El banco seleccionado no existe.',
        'routeVoucher.file'       => 'El comprobante debe ser un archivo válido.',

        // Retención
        'retencion.required_if' => 'Debes indicar la retención cuando el tipo de venta es Retención.',
        'retencion.numeric'     => 'La retención debe ser numérica.',
        'retencion.min'         => 'La retención no puede ser menor a 0%.',
        'retencion.max'         => 'La retención no puede superar 100%.',

        // Detracción
        'detractionCode.required_if'      => 'Debes indicar el código de bien cuando el tipo de venta es Detracción.',
        'detractionCode.string'           => 'El código de bien de detracción debe ser texto.',
        'detractionPercentage.required_if'=> 'Debes indicar el porcentaje de detracción cuando el tipo de venta es Detracción.',
        'detractionPercentage.numeric'    => 'El porcentaje de detracción debe ser numérico.',

        // Detalle de venta
        'saleDetails.required'                => 'Debes agregar al menos un ítem en el detalle.',
        'saleDetails.array'                   => 'El detalle de venta es inválido.',
        'saleDetails.*.description.required'  => 'La descripción del ítem es obligatoria.',
        'saleDetails.*.unit.required'         => 'La unidad del ítem es obligatoria.',
        'saleDetails.*.quantity.required'     => 'La cantidad del ítem es obligatoria.',
        'saleDetails.*.quantity.numeric'      => 'La cantidad del ítem debe ser numérica.',
        'saleDetails.*.unitValue.required'    => 'El valor unitario del ítem es obligatorio.',
        'saleDetails.*.unitValue.numeric'     => 'El valor unitario debe ser numérico.',
        'saleDetails.*.unitPrice.required'    => 'El precio unitario del ítem es obligatorio.',
        'saleDetails.*.unitPrice.numeric'     => 'El precio unitario del ítem debe ser numérico.',
        'saleDetails.*.discount.numeric'      => 'El descuento debe ser numérico.',
        'saleDetails.*.subTotal.required'     => 'El subtotal del ítem es obligatorio.',
        'saleDetails.*.subTotal.numeric'      => 'El subtotal debe ser numérico.',

        // Créditos / cuotas
        'commitments.required_if'             => 'Debes registrar las cuotas cuando el pago es a crédito.',
        'commitments.array'                   => 'El formato de cuotas es inválido.',
        'commitments.*.price.required'        => 'Cada cuota debe tener un monto.',
        'commitments.*.price.numeric'         => 'El monto de la cuota debe ser numérico.',
        'commitments.*.paymentDate.required'  => 'Cada cuota debe tener un número de días para el vencimiento.',
        'commitments.*.paymentDate.integer'   => 'El número de días de la cuota debe ser un entero.',
    ];
}

public function attributes()
{
    return [
        'paymentDate'    => 'fecha de pago',
        'documentType'   => 'tipo de documento',
        'saleType'       => 'tipo de venta',
        'paymentType'    => 'tipo de pago',
        'person_id'      => 'cliente',
        'budget_sheet_id'=> 'presupuesto',
        'yape'           => 'pago Yape',
        'deposit'        => 'depósito',
        'effective'      => 'efectivo',
        'card'           => 'tarjeta',
        'plin'           => 'pago Plin',
        'isBankPayment'  => 'pago bancario',
        'nro_operation'  => 'número de operación',
        'bank_id'        => 'banco',
        'routeVoucher'   => 'comprobante bancario',
        'comment'        => 'comentario',

        'retencion'             => 'retención (%)',
        'detractionCode'        => 'código de bien de detracción',
        'detractionPercentage'  => 'porcentaje de detracción',

        'saleDetails'               => 'detalle de venta',
        'saleDetails.*.description' => 'descripción del ítem',
        'saleDetails.*.unit'        => 'unidad del ítem',
        'saleDetails.*.quantity'    => 'cantidad del ítem',
        'saleDetails.*.unitValue'   => 'valor unitario del ítem',
        'saleDetails.*.unitPrice'   => 'precio unitario del ítem',
        'saleDetails.*.discount'    => 'descuento del ítem',
        'saleDetails.*.subTotal'    => 'subtotal del ítem',

        'commitments'                 => 'cuotas',
        'commitments.*.price'         => 'monto de la cuota',
        'commitments.*.paymentDate'   => 'días de vencimiento de la cuota',
    ];
}


}
