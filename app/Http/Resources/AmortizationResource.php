<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AmortizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sequentialNumber' => $this->sequentialNumber,
            'amount' => $this->amount,
            'paymentDate' => $this->paymentDate,
            'moviment_id' => $this->moviment_id,
            'commitment_id' => $this->commitment_id,
            'created_at' => $this->created_at,
            'balance' => $this->commitment->balance,
        ];
    }
}
