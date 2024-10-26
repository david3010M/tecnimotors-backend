<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'budget_sheet_id' => 'required|integer|exists:budget_sheets,id',
        ];
    }
}
