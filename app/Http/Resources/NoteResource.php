<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="NoteResource",
 *     title="NoteResource",
 *     description="Note resource",
 *     @OA\Property( property="id", type="integer", example="1"),
 *     @OA\Property( property="number", type="string", example="NOTA-123456"),
 *     @OA\Property( property="documentType", type="string", example="NOTA DE CRÉDITO"),
 *     @OA\Property( property="date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property( property="comment", type="string", example="comment"),
 *     @OA\Property( property="company", type="string", example="TECNIMOTORS"),
 *     @OA\Property( property="discount", type="number", example="10.00"),
 *     @OA\Property( property="totalCreditNote", type="number", example="100.00"),
 *     @OA\Property( property="totalDocumentReference", type="number", example="110.00"),
 *     @OA\Property( property="note_reason_id", type="integer", example="1"),
 *     @OA\Property( property="sale_id", type="integer", example="1"),
 *     @OA\Property( property="status", type="string", example="PENDING"),
 *     @OA\Property( property="sale", type="object", ref="#/components/schemas/SaleResource"),
 *     @OA\Property( property="details", type="array", @OA\Items(ref="#/components/schemas/SaleDetailResource")),
 *     @OA\Property( property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00")
 * )
 *
 *
 * @OA\Schema(
 *     schema="NoteCollection",
 *     title="NoteCollection",
 *     description="Note collection",
 *     @OA\Property( property="data", type="array", @OA\Items(ref="#/components/schemas/NoteResource")),
 *     @OA\Property( property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property( property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'documentType' => $this->documentType,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'comment' => $this->comment,
            'company' => $this->company,
            'discount' => $this->discount,
            'totalCreditNote' => $this->totalCreditNote,
            'totalDocumentReference' => $this->totalDocumentReference,
            'note_reason_id' => $this->note_reason_id,
            'sale_id' => $this->sale_id,
            'status' => $this->status,
            'sale' => new SaleResource($this->sale),
            'details' => SaleDetailResource::collection($this->saleDetails),
            'created_at' => $this->created_at,
        ];
    }
}
