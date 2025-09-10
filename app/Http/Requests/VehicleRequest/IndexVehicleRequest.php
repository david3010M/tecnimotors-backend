<?php

namespace App\Http\Requests\VehicleRequest;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexVehicleRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'from' => 'nullable|string',
            'to' => 'nullable|string',

            'plate' => 'nullable|string',
            'year' => 'nullable|string',
            'model' => 'nullable|string',
            'chasis' => 'nullable|string',
            'motor' => 'nullable|string',
            'codeBin' => 'nullable|string',
            'person_id' => 'nullable|string',
            'typeVehicle_id' => 'nullable|string',
            'vehicle_model_id' => 'nullable|string',
        ];
    }
}
