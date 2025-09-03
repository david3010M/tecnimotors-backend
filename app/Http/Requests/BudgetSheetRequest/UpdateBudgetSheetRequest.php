<?php

namespace App\Http\Requests\BudgetSheetRequest;

use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetSheetRequest extends UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount' => ['nullable', 'numeric', 'min:0'],

            // En update enviarás SOLO detalles
            'details' => ['required', 'array', 'min:1'],

            // Reglas por cada detalle
            'details.*.type'      => ['required', Rule::in(['Producto', 'Service'])],
            'details.*.saleprice' => ['required', 'numeric', 'min:0'],
            'details.*.quantity'  => ['required', 'integer', 'min:1'],
            'details.*.comment'   => ['nullable', 'string'],

            // Obligatorios según el tipo
            'details.*.service_id' => [
                'nullable',
                'integer',
                Rule::requiredIf(fn () => $this->rowRequires('Service')),
                Rule::exists('services', 'id'),
            ],
            'details.*.product_id' => [
                'nullable',
                'integer',
                Rule::requiredIf(fn () => $this->rowRequires('Producto')),
                Rule::exists('products', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'discount.numeric' => 'El descuento debe ser numérico.',
            'discount.min'     => 'El descuento no puede ser negativo.',

            'details.required' => 'Debe enviar la lista de detalles.',
            'details.array'    => 'El campo details debe ser un array.',
            'details.min'      => 'Debe enviar al menos un detalle.',

            'details.*.type.required' => 'Cada detalle debe incluir el tipo.',
            'details.*.type.in'       => 'El tipo debe ser "Producto" o "Service".',

            'details.*.saleprice.required' => 'El precio de venta es obligatorio.',
            'details.*.saleprice.numeric'  => 'El precio de venta debe ser numérico.',
            'details.*.saleprice.min'      => 'El precio de venta no puede ser negativo.',

            'details.*.quantity.required' => 'La cantidad es obligatoria.',
            'details.*.quantity.integer'  => 'La cantidad debe ser un entero.',
            'details.*.quantity.min'      => 'La cantidad mínima es 1.',

            'details.*.service_id.required' => 'El service_id es obligatorio si el tipo es "Service".',
            'details.*.service_id.exists'   => 'El service_id no existe.',
            'details.*.product_id.required' => 'El product_id es obligatorio si el tipo es "Producto".',
            'details.*.product_id.exists'   => 'El product_id no existe.',
        ];
    }

    /**
     * Indica si algún row de details requiere la clave según su tipo.
     */
    private function rowRequires(string $type): bool
    {
        $details = $this->input('details', []);
        foreach ($details as $row) {
            if (($row['type'] ?? null) === $type) {
                if ($type === 'Service'  && empty($row['service_id']))  return true;
                if ($type === 'Producto' && empty($row['product_id'])) return true;
            }
        }
        return false;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $details = $this->input('details', []);
            $discount = (float) $this->input('discount', 0);

            $total = collect($details)->sum(function ($d) {
                $quantity = (int) ($d['quantity'] ?? 0);
                $saleprice = (float) ($d['saleprice'] ?? 0);
                return $quantity * $saleprice;
            });

            if ($discount > $total) {
                $validator->errors()->add('discount', 'El descuento no puede superar el total de los montos de los detalles.');
            }
        });
    }
}
