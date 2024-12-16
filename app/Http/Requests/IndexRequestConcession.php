<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequestConcession extends IndexRequest
{
    public function rules(): array
    {
        return [
            'concession' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'client_id' => 'nullable|integer|exists:people,id',
            'concessionaire_id' => 'nullable|integer|exists:people,id',
        ];
    }
}
