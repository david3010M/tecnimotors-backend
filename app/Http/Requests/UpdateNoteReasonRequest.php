<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateNoteReasonRequest extends UpdateRequest
{
    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'code')->whereNull('deleted_at')->ignore($this->route('noteReason')),
            ],
            'description' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'description')->whereNull('deleted_at')->ignore($this->route('noteReason')),
            ]
        ];
    }
}
