<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;


/**
 * @OA\Schema(
 *     schema="CommitmentResource",
 *     title="Commitment",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="0001"),
 *     @OA\Property(property="client", type="string", example="Juan Perez"),
 *     @OA\Property(property="payment_type", type="string", example="Semanal"),
 *     @OA\Property(property="payment_date", type="string", example="2024-06-27 22:59:36"),
 *     @OA\Property(property="price", type="decimal", example="1000.00"),
 *     @OA\Property(property="amount_paid", type="decimal", example="100.00"),
 *     @OA\Property(property="balance", type="decimal", example="900.00"),
 *     @OA\Property(property="dues", type="integer", example="10"),
 *     @OA\Property(property="payment_pending", type="integer", example="1"),
 *     @OA\Property(property="status", type="string", example="Pendiente"),
 *     @OA\Property(property="sale_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", example="2024-06-27 22:59:36")
 * )
 *
 *
 * @OA\Schema(
 *     schema="CommitmentCollectionPagination",
 *     title="CommitmentCollectionPagination",
 *     @OA\Property(property="current_page", type="integer", example="1"),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CommitmentResource")),
 *     @OA\Property(property="first_page_url", type="string", example="https://develop.garzasoft.com/tecnimotors-backend/public/api/commitment?page=1"),
 *     @OA\Property(property="from", type="integer", example="1"),
 *     @OA\Property(property="next_page_url", type="string", example="null"),
 *     @OA\Property(property="path", type="string", example="https://develop.garzasoft.com/tecnimotors-backend/public/api/commitment"),
 *     @OA\Property(property="per_page", type="integer", example="5"),
 *     @OA\Property(property="prev_page_url", type="string", example="null"),
 *     @OA\Property(property="to", type="integer", example="5"),
 * )
 */
class CommitmentResource extends JsonResource
{
    public function toArray($request)
    {
        $client = $this->sale->person ? ($this->sale->person->typeofDocument == 'DNI' ?
            ($this->sale->person->names . ' ' .
                $this->sale->person->fatherSurname . ' ' .
                $this->sale->person->motherSurname) :
            $this->sale->person->businessName) : 'NO ASIGNADO';

        return [
            'id' => $this->id,
            'number' => $this->sale?->budgetSheet?->number?? null,
            'client' => $client,
            'payment_date' => $this->payment_date ? Carbon::parse($this->payment_date)->format('d-m-Y') : null,
            'payment_type' => $this->payment_type,
            'price' => $this->price,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'numberQuota' => $this->numberQuota,
            'status' => $this->status,
            'sale_id' => $this->sale_id,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
        ];
    }
}
