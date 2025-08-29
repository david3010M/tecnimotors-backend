<?php

namespace App\Http\Requests\ModelVehicleRequest;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequestModelVehicle extends IndexRequest
{
    public function rules(): array
    {
        return [
            // rangos de fecha
            'from' => 'nullable|string',
            'to'   => 'nullable|string',

            // filtros adicionales
            'name'             => 'nullable|string',
            'brand_id'         => 'nullable|string',
        ];
    }
}
