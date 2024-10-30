<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetSheetDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'paymentType' => $this->paymentType,
            'totalService' => $this->totalService,
            'totalProducts' => $this->totalProducts,
            'debtAmount' => $this->debtAmount,
            'total' => $this->total,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'igv' => $this->igv,
            'status' => $this->status,
            'attention_id' => $this->attention_id,
            'created_at' => $this->created_at,
        ];
    }
}
