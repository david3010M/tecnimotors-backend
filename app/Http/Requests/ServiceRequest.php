<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            "plate" => "required",
            "from" => "nullable|date",
            "to" => "nullable|date",
        ];
    }
}
