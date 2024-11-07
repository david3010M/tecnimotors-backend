<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleReportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'number' => 'nullable|string',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'documentType' => 'nullable|string|in:BOLETA,FACTURA,TICKET',
            'saleType' => 'nullable|string|in:NORMAL,DETRACCION',
            'paymentType' => 'nullable|string|in:CONTADO,CREDITO',
            'status' => 'nullable|string',
            'person_id' => 'nullable|integer',
            'person$documentNumber' => 'nullable|string',
            'budget_sheet_id' => 'nullable|integer',
        ];
    }
}
