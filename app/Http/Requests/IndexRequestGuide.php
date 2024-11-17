<?php

namespace App\Http\Requests;


class IndexRequestGuide extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable',
            'date_emision' => 'nullable',
            'recipient_names' => 'nullable|string',
            'guide_motive_id' => 'nullable|integer',
            'driver_fullnames' => 'nullable|string',
            'districtStart$name' => 'nullable|string',
            'districtEnd$name' => 'nullable|string',
            'observation' => 'nullable|string',
        ];
    }
}
