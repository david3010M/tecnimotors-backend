<?php

namespace App\Http\Requests;


class IndexRequestNoteReason extends IndexRequest
{
    public function rules(): array
    {
        return [
            'code' => 'string|max:255',
            'description' => 'string|max:255',
        ];
    }
}
