<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateConcessionRequest",
 *     title="UpdateConcessionRequest",
 *     description="Update concession request",
 *     @OA\Property(property="concession", type="string", example="Concesion 1"),
 *     @OA\Property(property="registerDate", type="string", example="2021-09-01"),
 *     @OA\Property(property="client_id", type="integer", example="1"),
 *     @OA\Property(property="concessionaire_id", type="integer", example="2"),
 *     @OA\Property(property="logo", type="file", format="binary"),
 * )
 */
class UpdateConcessionRequest extends UpdateRequest
{
    public function rules()
    {
        return [
            'concession' => [
                'nullable',
                'string',
                Rule::unique('concessions', 'concession')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('id')),
            ],
            'registerDate' => 'nullable|date',
            'client_id' => 'nullable|integer|exists:people,id',
            'concessionaire_id' => 'nullable|integer|exists:people,id',
        ];
    }
}
