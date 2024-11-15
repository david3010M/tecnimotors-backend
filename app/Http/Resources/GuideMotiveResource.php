<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="GuideMotiveResource",
 *     title="GuideMotiveResource",
 *     description="Guide motive resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="code", type="string", example="01"),
 *     @OA\Property (property="name", type="string", example="Venta"),
 *     @OA\Property (property="created_at", type="string", format="date-time", example="2024-08-19T00:00:00.000000Z")
 * )
 */
class GuideMotiveResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'created_at' => $this->created_at,
        ];
    }
}
