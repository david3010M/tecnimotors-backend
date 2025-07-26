<?php

namespace App\Http\Requests\DetailBudgetRequest;

use App\Http\Requests\IndexRequest;

class IndexDetailBudgetRequest extends IndexRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleprice'     => ['nullable', 'string'],
            'quantity'      => ['nullable', 'string'],
            'type'          => ['nullable', 'string'],
            'comment'       => ['nullable', 'string'],
            'status'        => ['nullable', 'string'],
            'dateRegister'  => ['nullable', 'string'],
            'dateMax'       => ['nullable', 'string'],
            'dateCurrent'   => ['nullable', 'string'],
            'percentage'    => ['nullable', 'string'],
            'period'        => ['nullable', 'string'],
            'attention_id'  => ['nullable', 'string'],
            'worker_id'     => ['nullable', 'string'],
            'service_id'    => ['nullable', 'string'],
            'product_id'    => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'saleprice.string'    => 'El campo saleprice debe ser una cadena.',
            'quantity.string'     => 'El campo quantity debe ser una cadena.',
            'type.string'         => 'El campo type debe ser una cadena.',
            'comment.string'      => 'El campo comment debe ser una cadena.',
            'status.string'       => 'El campo status debe ser una cadena.',
            'dateRegister.string' => 'El campo dateRegister debe ser una cadena.',
            'dateMax.string'      => 'El campo dateMax debe ser una cadena.',
            'dateCurrent.string'  => 'El campo dateCurrent debe ser una cadena.',
            'percentage.string'   => 'El campo percentage debe ser una cadena.',
            'period.string'       => 'El campo period debe ser una cadena.',
            'attention_id.string' => 'El campo attention_id debe ser una cadena.',
            'worker_id.string'    => 'El campo worker_id debe ser una cadena.',
            'service_id.string'   => 'El campo service_id debe ser una cadena.',
            'product_id.string'   => 'El campo product_id debe ser una cadena.',
        ];
    }
}
