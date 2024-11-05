<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreNoteReasonRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'code')->whereNull('deleted_at'),
            ],
            'description' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'description')->whereNull('deleted_at'),
            ]
        ];
    }
}
