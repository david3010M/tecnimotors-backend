<?php

namespace App\Http\Requests;


class IndexRequestGuide extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable',
            'recipient_names' => 'nullable|string',
            'guide_motive_id' => 'nullable|integer',
            'driver_names+driver_surnames' => 'nullable|string',
            'districtStart$name' => 'nullable|string',
            'districtEnd$name' => 'nullable|string',
            'observation' => 'nullable|string',
        ];
    }
}
