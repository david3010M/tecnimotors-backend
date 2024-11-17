<?php

namespace App\Http\Requests;


class IndexRequestGuide extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'recipient_names' => 'nullable|string',
            'guide_motive_id' => 'nullable|integer',
            'driver_fullnames' => 'nullable|string',
            'districtStart$name' => 'nullable|string',
            'districtEnd$name' => 'nullable|string',
            'observation' => 'nullable|string',
        ];
    }
}
