<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovementClientRequest extends FormRequest
{
    public function rules()
    {
        return [
            "from" => "nullable|date",
            "to" => "nullable|date",
        ];
    }
}
