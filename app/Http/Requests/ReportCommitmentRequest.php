<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportCommitmentRequest extends FormRequest
{

    public function rules()
    {
        return [
            "cliente_id" => "nullable|exists:people,id",
            "status" => "nullable|in:Pendiente,Pagado",
            "from" => "nullable|date",
            "to" => "nullable|date",
        ];
    }
}
