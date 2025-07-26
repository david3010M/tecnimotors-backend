<?php

namespace App\Http\Requests\DetailBudgetRequest;

use App\Http\Requests\UpdateRequest;

/**
 * @OA\Schema(
 *     schema="UpdateDetailBudgetRequest",
 *     @OA\Property(property="saleprice", type="number", format="float"),
 *     @OA\Property(property="quantity", type="integer"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="dateRegister", type="string", format="date"),
 *     @OA\Property(property="dateMax", type="string", format="date"),
 *     @OA\Property(property="dateCurrent", type="string", format="date"),
 *     @OA\Property(property="percentage", type="integer"),
 *     @OA\Property(property="period", type="integer"),
 *     @OA\Property(property="budget_sheet_id", type="integer"),
 *     @OA\Property(property="worker_id", type="integer"),
 *     @OA\Property(property="service_id", type="integer"),
 *     @OA\Property(property="product_id", type="integer")
 * )
 */
class UpdateDetailBudgetRequest extends UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'saleprice' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
            'status' => 'nullable|string|in:Pendiente,En Curso,Finalizado',
            'dateRegister' => ['nullable', 'date'],
            'dateMax' => ['nullable', 'date'],
            'dateCurrent' => ['nullable', 'date'],
            'percentage' => ['nullable', 'integer'],
            'period' => ['nullable', 'integer'],
            'budget_sheet_id' => ['nullable', 'exists:budget_sheets,id'],
            'worker_id' => ['nullable', 'exists:workers,id'],
            'product_id' => ['required_if:type,Producto', 'nullable', 'exists:products,id'],
            'service_id' => ['required_if:type,Service', 'nullable', 'exists:services,id'],
        ];
    }

    public function messages()
    {
        return [
            'saleprice.numeric' => 'El campo saleprice debe ser un número.',
            'quantity.integer' => 'El campo quantity debe ser un número entero.',
            'type.string' => 'El campo type debe ser una cadena de texto.',
            'comment.string' => 'El campo comment debe ser una cadena de texto.',
            'status.string' => 'El campo status debe ser una cadena de texto.',
            'dateRegister.date' => 'El campo dateRegister debe ser una fecha válida.',
            'dateMax.date' => 'El campo dateMax debe ser una fecha válida.',
            'dateCurrent.date' => 'El campo dateCurrent debe ser una fecha válida.',
            'percentage.integer' => 'El campo percentage debe ser un número entero.',
            'period.integer' => 'El campo period debe ser un número entero.',
            'budget_sheet_id.exists' => 'El budget_sheet_id debe existir en la tabla budget_sheets.',
            'worker_id.exists' => 'El worker_id debe existir en la tabla workers.',
            'service_id.exists' => 'El service_id debe existir en la tabla services.',
            'product_id.exists' => 'El product_id debe existir en la tabla products.',
        ];
    }
}
