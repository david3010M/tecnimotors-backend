<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="SaleDetailResource",
 *     @OA\Property(property="description", type="string", example="Producto 1"),
 *     @OA\Property(property="unit", type="string", example="UNIDAD"),
 *     @OA\Property(property="quantity", type="number", example="10"),
 *     @OA\Property(property="unitValue", type="number", example="10.00"),
 *     @OA\Property(property="unitPrice", type="number", example="10.00"),
 *     @OA\Property(property="discount", type="number", example="0.00"),
 *     @OA\Property(property="subTotal", type="number", example="100.00"),
 *     @OA\Property(property="sale_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00")
 * )
 *
 * @OA\Schema (
 *     schema="SaleDetailCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SaleDetailResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class SaleDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
