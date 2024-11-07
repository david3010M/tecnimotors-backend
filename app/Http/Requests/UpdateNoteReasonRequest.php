<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     title="UpdateNoteReasonRequest",
 *     required={"code", "description"},
 *     @OA\Property( property="code", type="string", example="1", description="The code of the note reason"),
 *     @OA\Property( property="description", type="string", example="description", description="The description of the note reason")
 * )
 */
class UpdateNoteReasonRequest extends UpdateRequest
{
    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'code')->whereNull('deleted_at')->ignore($this->route('noteReason')),
            ],
            'description' => [
                'required',
                'string',
                'max:255',
                Rule::unique('note_reasons', 'description')->whereNull('deleted_at')->ignore($this->route('noteReason')),
            ]
        ];
    }
}