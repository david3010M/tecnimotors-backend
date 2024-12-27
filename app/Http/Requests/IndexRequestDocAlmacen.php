<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequestDocAlmacen extends IndexRequest
{
    public function rules(): array
    {
        return [
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'comment' => 'nullable|string',
            'user$username' => 'nullable|string',
            'product$name' => 'nullable|string',
            'concept_mov$name' => 'nullable|string',
        ];
    }
}
