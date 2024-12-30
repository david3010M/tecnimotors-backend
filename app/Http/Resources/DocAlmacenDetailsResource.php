<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocAlmacenDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sequentialnumber' => $this->sequentialnumber,
            'quantity' => $this->quantity,
            'comment' => $this->comment,
            'product_id' => $this->product_id,
            'doc_almacen_id' => $this->doc_almacen_id,
            'created_at' => $this->created_at,
            'product' => $this->product,
        ];
    }
}
