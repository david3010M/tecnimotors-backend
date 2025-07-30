<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetSheetResource extends JsonResource
{
    public function toArray($request)
    {
        $name = $this->attention?->vehicle?->person?->typeofDocument == 'RUC' ? $this->attention?->vehicle?->person?->businessName : $this->attention?->vehicle?->person?->names . ' ' . $this->attention?->vehicle?->person?->fatherSurname . ' ' . $this->attention?->vehicle?->person?->motherSurname;
        $number = $this->number ?? '';
        return [
            'id' => $this->id,
            'number' => $number,
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
            'attention' => $this->attention,
            'vehicle_id' => $this->attention?->vehicle_id ?? null,
            'vehicle_plate' => $this->attention?->vehicle?->plate ?? null,

            'person_id' => $this->attention?->vehicle?->person_id ?? null,
            'person_name' => $name ?? null,

            'details' => $this->details,
            'created_at' => $this->created_at,
        ];
    }
}
