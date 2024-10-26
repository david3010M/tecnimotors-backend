<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSaleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => 'nullable|string',
            'paymentDate' => 'nullable|array|size:2',
            'paymentDate.0' => 'nullable|date_format:Y-m-d',
            'paymentDate.1' => 'nullable|date_format:Y-m-d',
            'documentType' => 'nullable|string|in:BOLETA,FACTURA,TICKET',
            'saleType' => 'nullable|string|in:NORMAL,DETRACCION',
            'detractionCode' => 'nullable|string',
            'detractionPercentage' => 'nullable|string',
            'paymentType' => 'nullable|string|in:CONTADO,CREDITO',
            'status' => 'nullable|string',
            'person_id' => 'nullable|integer',
            'person$documentNumber' => 'nullable|string',
            'budget_sheet_id' => 'nullable|integer',
        ];
    }
}
