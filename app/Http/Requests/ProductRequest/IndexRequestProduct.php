<?php

namespace App\Http\Requests\ProductRequest;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequestProduct extends IndexRequest
{
    public function rules(): array
    {
        return [
            // rangos de fecha
            'from' => 'nullable|string',
            'to'   => 'nullable|string',

            // filtros adicionales
            'sequentialnumber' => 'nullable|string',
            'name'             => 'nullable|string',
            'purchase_price'   => 'nullable|string',
            'percentage'       => 'nullable|string',
            'sale_price'       => 'nullable|string',
            'stock'            => 'nullable|string',
            'quantity'         => 'nullable|string',
            'type'             => 'nullable|string',
            'category_id'      => 'nullable|string',
            'unit_id'          => 'nullable|string',
            'brand_id'         => 'nullable|string',
        ];
    }
}
