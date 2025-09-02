<?php

namespace App\Http\Requests;

use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\{Sale, Moviment};
use Illuminate\Validation\Validator;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Principales
            'paymentDate' => 'required|date',
            'documentType' => 'required|in:' .
                Constants::SALE_BOLETA . ',' .
                Constants::SALE_FACTURA . ',' .
                Constants::SALE_TICKET . ',' .
                Constants::SALE_NOTA_CREDITO_BOLETA . ',' .
                Constants::SALE_NOTA_CREDITO_FACTURA,
            'paymentType' => 'required|in:' . Constants::SALE_CONTADO . ',' . Constants::SALE_CREDITO,
            'saleType' => 'required|in:' . Constants::SALE_NORMAL . ',' . Constants::SALE_DETRACCION . ',' . Constants::SALE_RETENCION,
            'person_id' => 'required|integer|exists:people,id',
            'budget_sheet_id' => 'nullable|integer|exists:budget_sheets,id',

            // Bancos / pagos
            'yape' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'effective' => 'nullable|numeric',
            'card' => 'nullable|numeric',
            'plin' => 'nullable|numeric',
            'isBankPayment' => 'required|in:0,1',
            'nro_operation' => 'nullable|string',
            'bank_id' => 'nullable|exists:banks,id',
            'numberVoucher' => 'nullable|string',
            'routeVoucher' => 'nullable|file',
            'comment' => 'nullable|string',
            'paymentConcept_id' => 'nullable|integer',

            // Detracción / retención
            'retencion' => 'nullable|numeric|min:0|max:100|required_if:saleType,' . Constants::SALE_RETENCION,
            'detractionCode' => 'nullable|required_if:saleType,' . Constants::SALE_DETRACCION . '|string',
            'detractionPercentage' => 'nullable|min:0|max:100|required_if:saleType,' . Constants::SALE_DETRACCION . '|numeric',

            // Detalles
            'saleDetails' => 'required|array|min:1',
            'saleDetails.*.id' => 'nullable|integer|exists:sale_details,id',
            'saleDetails.*.description' => 'required|string',
            'saleDetails.*.unit' => 'required|string',
            'saleDetails.*.quantity' => 'required|numeric',
            'saleDetails.*.unitValue' => 'required|numeric',
            'saleDetails.*.unitPrice' => 'required|numeric',
            'saleDetails.*.discount' => 'nullable|numeric',
            'saleDetails.*.subTotal' => 'required|numeric',

            // Cuotas (crédito)
            'commitments' => 'required_if:paymentType,' . Constants::SALE_CREDITO . '|array',
            'commitments.*.price' => 'required_if:paymentType,' . Constants::SALE_CREDITO . '|numeric',
            'commitments.*.paymentDate' => 'required_if:paymentType,' . Constants::SALE_CREDITO ,
        ];
    }

    public function prepareForValidation(): void
    {
        // Normaliza montos nulos a 0 para cálculos
        $this->merge([
            'yape' => $this->yape ?? 0,
            'deposit' => $this->deposit ?? 0,
            'effective' => $this->effective ?? 0,
            'card' => $this->card ?? 0,
            'plin' => $this->plin ?? 0,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $sale = Sale::find($this->route('id'));
            if (!$sale) {
                $v->errors()->add('sale', 'Venta no encontrada.');
                return;
            }

            // 🔒 No permitir cambiar el tipo de documento
            if ($sale->documentType !== $this->documentType) {
                $v->errors()->add('documentType', 'No se permite cambiar el tipo de documento (correlativo).');
            }

            // 🔒 Validar que la fecha de pago sea hoy
            if ($this->paymentDate !== now()->toDateString()) {
                $v->errors()->add('paymentDate', 'Solo se puede actualizar si la fecha de pago es la fecha actual.');
            }

            // Totales calculados desde detalle
            $taxable = collect($this->saleDetails ?? [])->sum(
                fn($d) => (float) $d['unitValue'] * (float) $d['quantity']
            );
            $igv = round($taxable * Constants::IGV, 2);
            $total = round($taxable + $igv, 2);

            // Guarda cálculos para el controlador
            $this->merge([
                '__calc_taxable' => $taxable,
                '__calc_igv' => $igv,
                '__calc_total' => $total,
            ]);

            if ($this->paymentType === Constants::SALE_CONTADO) {
                // Reglas de caja
                $movCaja = Moviment::where('status', 'Activa')->where('paymentConcept_id', 1)->first();
                $pc = (int) ($this->paymentConcept_id ?? 0);
                if (!$movCaja && $pc != 1) {
                    $v->errors()->add('paymentConcept_id', 'Debe Aperturar Caja.');
                }
                if ($movCaja && $pc == 1) {
                    $v->errors()->add('paymentConcept_id', 'Caja Ya Aperturada.');
                }

                // Validación de pagos vs total
                $pagos = (float) $this->effective + (float) $this->yape + (float) $this->plin
                    + (float) $this->card + (float) $this->deposit;

                if ($pagos <= 0) {
                    $v->errors()->add('payments', 'El monto a pagar no puede ser 0.');
                }
                if (round($total - $pagos, 1) != 0) {
                    $v->errors()->add('payments', 'El monto a pagar no coincide con el total (dif: ' . round($total - $pagos, 1) . ').');
                }
            } else {
                // Validación de cuotas vs total
                $sum = collect($this->commitments ?? [])->sum('price');
                if (round($total - $sum, 1) != 0) {
                    $v->errors()->add('commitments', 'La suma de las cuotas no coincide con el total (dif: ' . round($total - $sum, 1) . ').');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'paymentDate' => 'fecha de pago',
            'documentType' => 'tipo de documento',
            'paymentType' => 'tipo de pago',
            'saleType' => 'tipo de venta',
            'person_id' => 'cliente',
            'routeVoucher' => 'comprobante bancario',
            'commitments' => 'cuotas',
            'saleDetails' => 'detalle de venta',
        ];
    }
}
