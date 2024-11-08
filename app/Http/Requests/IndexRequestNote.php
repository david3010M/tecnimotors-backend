<?php

namespace App\Http\Requests;

class IndexRequestNote extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'sale$number' => 'nullable|string',
            'sale$person_id' => 'nullable|integer',
        ];
    }
}
