<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteReportRequest extends FormRequest
{
    public function rules()
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
