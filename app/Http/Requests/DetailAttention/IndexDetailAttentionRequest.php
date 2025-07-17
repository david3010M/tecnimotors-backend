<?php

namespace App\Http\Requests\DetailAttention;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexDetailAttentionRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            
            'saleprice' => 'nullable|string',
            'quantity' => 'nullable|string',
            'type' => 'nullable|string',
            'comment' => 'nullable|string',
            'status' => 'nullable|string',
            'dateRegister' => 'nullable|string',
            'dateMax' => 'nullable|string',
            'dateCurrent' => 'nullable|string',
            'percentage' => 'nullable|string',
            'period' => 'nullable|string',
            'service_id' => 'nullable|string',
            'product_id' => 'nullable|string',
            'worker_id' => 'nullable|string',
            'attention_id' => 'nullable|string',
        ];
    }
}
