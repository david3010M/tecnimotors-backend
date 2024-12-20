<?php

namespace App\Http\Requests;

class IndexRequestSale extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
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
