<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="NoteReasonResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="01 | REASON 1"),
 *     @OA\Property(property="code", type="string", example="01"),
 *     @OA\Property(property="description", type="string", example="REASON 1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00")
 * )
 *
 * @OA\Schema (
 *     schema="NoteReasonCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/NoteReasonResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class NoteReasonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->code . ' | ' . $this->description,
            'code' => $this->code,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
