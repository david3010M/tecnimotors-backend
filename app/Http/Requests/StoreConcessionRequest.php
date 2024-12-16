<?php

namespace App\Http\Requests;


use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreConcessionRequest",
 *     title="StoreConcessionRequest",
 *     description="Store concession request",
 *     required={"concession", "client_id", "concessionaire_id"},
 *     @OA\Property(property="concession", type="string", example="Concesion 1"),
 *     @OA\Property(property="registerDate", type="string", example="2021-09-01"),
 *     @OA\Property(property="client_id", type="integer", example="1"),
 *     @OA\Property(property="concessionaire_id", type="integer", example="2"),
 *     @OA\Property(property="logo", type="file", format="binary"),
 * )
 */
class StoreConcessionRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'concession' => [
                'required',
                'string',
                'max:255',
                Rule::unique('concessions', 'concession')->whereNull('deleted_at'),
            ],
            'registerDate' => 'nullable|date',
            'client_id' => 'required|integer|exists:people,id',
            'concessionaire_id' => 'required|integer|exists:people,id|different:client_id',
        ];
    }
}
