<?php

namespace App\Http\Requests\BudgetSheetRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\DB;

class StoreBudgetSheetRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attention_id' => 'required|exists:attentions,id',
            'details' => 'required|array|min:1',
            'details.*.saleprice' => 'required|numeric|min:0',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.type' => 'required|in:Producto,Service',
            'details.*.comment' => 'nullable|string',
            'details.*.service_id' => 'nullable|required_if:details.*.type,Service|exists:services,id',
            'details.*.product_id' => 'nullable|required_if:details.*.type,Producto|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'details.*.type.in' => 'El tipo debe ser "Producto" o "Service".',
            'details.*.service_id.required_if' => 'El campo service_id es obligatorio si el tipo es "Service".',
            'details.*.product_id.required_if' => 'El campo product_id es obligatorio si el tipo es "Producto".',
            'details.*.saleprice.required' => 'El campo saleprice es obligatorio.',
            'details.*.saleprice.numeric' => 'El campo saleprice debe ser un número.',
            'details.*.quantity.required' => 'El campo quantity es obligatorio.',
            'details.*.quantity.integer' => 'El campo quantity debe ser un número entero.',
            // 'details.*.comment.string' => 'El campo comment debe ser una cadena de texto.',
            'details.*.service_id.exists' => 'El campo service_id debe existir en la tabla services.',
            'details.*.product_id.exists' => 'El campo product_id debe existir en la tabla products.',
            'attention_id.required' => 'El campo attention_id es obligatorio.',
            'attention_id.exists' => 'El campo attention_id debe existir en la tabla attentions.',
            'details.required' => 'Debe contener al menos un detalle.',
            'details.array' => 'El campo details debe ser un array.',
            'details.min' => 'El campo details debe contener al menos un elemento.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $attentionId = $this->input('attention_id');

            $alreadyExists = DB::table('budget_sheets')
                ->where('attention_id', $attentionId)
                ->whereNull('deleted_at')
                ->exists();

            if ($alreadyExists) {
                $validator->errors()->add('attention_id', 'Ya existe un presupuesto activo para esta atención.');
            }
        });
    }
}
