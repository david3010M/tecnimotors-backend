<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'plate' => $this->plate,
            'year' => $this->year,
            'model' => $this->model,
            'chasis' => $this->chasis,
            'motor' => $this->motor,
            'codeBin' => $this->codeBin,
            'person_id' => $this->person_id,
            'person_name' => $this?->person?->names . ' ' . $this?->person?->fatherSurname . ' ' . $this?->person?->motherSurname,
            'typeVehicle_id' => $this->typeVehicle_id,
            'typeVehicle_name' => $this->typeVehicle?->name,
            'vehicle_model_id' => $this->vehicle_model_id,
            'vehicleModel_name' => $this->vehicleModel?->name,
            'brand_name'        => $this->vehicleModel?->brand?->name,

            'created_at' => $this->created_at,

            // Relaciones simplificadas con ?->

        ];
    }
}
