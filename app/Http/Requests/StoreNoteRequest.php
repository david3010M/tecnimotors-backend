<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     title="StoreNoteRequest",
 *     description="Store note request",
 *     type="object",
 *     required={"date", "discount", "note_reason_id", "sale_id"},
 *     @OA\Property( property="date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property( property="comment", type="string", example="comment"),
 *     @OA\Property( property="discount", type="number", example="10.00"),
 *     @OA\Property( property="totalCreditNote", type="number", example="100.00"),
 *     @OA\Property( property="totalDocumentReference", type="number", example="110.00"),
 *     @OA\Property( property="note_reason_id", type="integer", example="1"),
 *     @OA\Property( property="sale_id", type="integer", example="1")
 * )
 */
class StoreNoteRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'discount' => 'required|numeric',
            'totalCreditNote' => 'nullable|numeric',
            'totalDocumentReference' => 'nullable|numeric',
            'note_reason_id' => 'required|exists:note_reasons,id',
            'sale_id' => 'required|exists:sales,id',
        ];
    }
}
