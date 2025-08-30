<?php

namespace App\Http\Requests\BrandRequest;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequestBrand extends IndexRequest
{
    public function rules(): array
    {
        return [
            // rangos de fecha
            'from' => 'nullable|string',
            'to'   => 'nullable|string',

            // filtros adicionales
            
            'name'             => 'nullable|string',
            'type'             => 'nullable|string',
        ];
    }
}
