<?php

namespace App\Http\Requests;

class IndexRequestNote extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'sale$number' => 'nullable|string',
            'sale$person_id' => 'nullable|integer',
        ];
    }
}
