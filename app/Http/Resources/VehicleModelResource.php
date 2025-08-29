<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'brand_id' => $this->brand_id ?? null,
            'brand_name' => $this?->brand?->name ?? null,
            'created_at' => $this->created_at ?? null,
        ];
    }
}
