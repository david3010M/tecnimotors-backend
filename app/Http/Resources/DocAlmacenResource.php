<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocAlmacenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date_moviment' => $this->date_moviment,
            'sequentialnumber' => 'DOCA-' . $this->sequentialnumber,
            'quantity' => $this->quantity,
            'comment' => $this->comment,
            'typemov' => $this->typemov,
            'concept' => $this->concept,

            'user_id' => $this->user_id,
            'attention_id' => $this->attention_id,
            'person_id' => $this->person_id,
            'concept_mov_id' => $this->concept_mov_id,
            'created_at' => $this->created_at,
            'user' => $this->user,
            'person' => $this->person,
            'concept_mov' => $this->concept_mov,
            'product' => $this->product,
            'details' => $this->details,
        ];
    }
}
