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
            'worker_id' => 'nullable|integer',
            'district_id_start' => 'nullable|string',
            'district_id_end' => 'nullable|string',
            'observation' => 'nullable|string',
        ];
    }
}
