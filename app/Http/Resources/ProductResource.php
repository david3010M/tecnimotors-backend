<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'purchase_price' => $this->purchase_price ?? null,
            'percentage' => $this->percentage ?? null,
            'sale_price' => $this->sale_price ?? null,
            'stock' => $this->stock ?? 0,
            'quantity' => $this->quantity ?? 0,
            'type' => $this->type ?? 'Repuesto',
            'category_id' => $this->category_id ?? null,
            'category_name' => $this?->category?->name ?? null,
            'unit_id' => $this->unit_id ?? null,
            'unit_code' => $this?->unit?->code ?? null,
            'brand_id' => $this->brand_id ?? null,
            'brand_name' => $this?->brand?->name ?? null,
            'created_at' => $this->created_at ?? null,
        ];
    }
}
