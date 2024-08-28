<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleProductReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plate' => "nullable|string|exists:vehicles,plate",
            'product_id' => "nullable|integer|exists:products,id",
            'from' => "nullable|date",
            'to' => "nullable|date",
        ];
    }
}
