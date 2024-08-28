<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->detail->dateRegister,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'quantity' => $this->detail->quantity,
            'total' => $this->detail->saleprice,
            'stock' => $this->stock,
            'type' => $this->type,
            'category' => $this->category->name,
            'unit' => $this->unit->name,
            'brand' => $this->brand->name, 'detail' => $this->detail->comment,
            'attentionNumber' => $this->detail->attention->number,
            'budgetSheetNumber' => $this->detail->attention->budgetSheet->number,
            'detail_id' => $this->detail->id,
            'attention_id' => $this->detail->attention_id,
            'vechicle_id' => $this->detail->attention->vehicle->id,
            'budgetSheet_id' => $this->detail->attention->budgetSheet->id,

        ];
    }
}
