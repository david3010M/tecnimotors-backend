<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ConcessionResource",
 *     title="ConcessionResource",
 *     description="Concession resource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="concession", type="string", example="Concesion 1"),
 *     @OA\Property(property="registerDate", type="string", example="2021-09-01"),
 *     @OA\Property(property="client", type="string", ref="#/components/schemas/Person"),
 *     @OA\Property(property="concessionaire", type="string", ref="#/components/schemas/Person"),
 *     @OA\Property(property="logo", type="string", example="http://localhost/tecnimotors-backend/storage/app/public/concessions/20210901000000_logo.png"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="ConcessionCollection",
 *     title="ConcessionCollection",
 *     description="Concession collection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ConcessionResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class ConcessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'concession' => $this->concession,
            'registerDate' => $this->registerDate?->format('Y-m-d'),
            'client' => $this->client,
            'concessionaire' => $this->concessionaire,
            'logo' => $this->routeImage?->route,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
