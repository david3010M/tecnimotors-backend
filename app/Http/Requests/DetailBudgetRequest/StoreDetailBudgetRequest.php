<?php

namespace App\Http\Requests\DetailBudgetRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreDetailBudgetRequest",
 *     required={"saleprice", "quantity", "type", "comment", "dateRegister", "dateMax", "dateCurrent", "percentage", "period", "budget_sheet_id", "worker_id"},
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
 *     @OA\Property(property="service_id", type="integer", nullable=true),
 *     @OA\Property(property="product_id", type="integer", nullable=true)
 * )
 */
class StoreDetailBudgetRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleprice' => ['required', 'numeric'],
            'quantity' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'comment' => ['required', 'string'],
            'status' => 'nullable|string|in:Pendiente,En Curso,Finalizado',

            'dateRegister'     => ['nullable', 'date'],
            'dateMax'          => ['nullable', 'date'],
            'dateCurrent'      => ['nullable', 'date'],
            'percentage'       => ['nullable', 'integer'],
            'period'           => ['nullable', 'integer'],
            'budget_sheet_id' => ['required', 'exists:budget_sheets,id'],
            'worker_id'        => ['nullable', 'exists:workers,id'],
            'product_id' => ['required_if:type,Producto', 'nullable', 'exists:products,id'],
            'service_id' => ['required_if:type,Service', 'nullable', 'exists:services,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'saleprice.required' => 'El campo saleprice es obligatorio.',
            'saleprice.numeric' => 'El campo saleprice debe ser un número.',
            'quantity.required' => 'El campo quantity es obligatorio.',
            'quantity.integer' => 'El campo quantity debe ser un número entero.',
            'type.required' => 'El campo type es obligatorio.',
            'type.string' => 'El campo type debe ser una cadena de texto.',
            'comment.required' => 'El campo comment es obligatorio.',
            'comment.string' => 'El campo comment debe ser una cadena de texto.',
            'status.string' => 'El campo status debe ser una cadena de texto.',
            'dateRegister.required' => 'El campo dateRegister es obligatorio.',
            'dateRegister.date' => 'El campo dateRegister debe ser una fecha válida.',
            'dateMax.required' => 'El campo dateMax es obligatorio.',
            'dateMax.date' => 'El campo dateMax debe ser una fecha válida.',
            'dateCurrent.required' => 'El campo dateCurrent es obligatorio.',
            'dateCurrent.date' => 'El campo dateCurrent debe ser una fecha válida.',
            'percentage.required' => 'El campo percentage es obligatorio.',
            'percentage.integer' => 'El campo percentage debe ser un número entero.',
            'period.required' => 'El campo period es obligatorio.',
            'period.integer' => 'El campo period debe ser un número entero.',
            'budget_sheet_id.required' => 'El campo budget_sheet_id es obligatorio.',
            'budget_sheet_id.exists' => 'El budget_sheet_id debe existir en la tabla budget_sheets.',
            'worker_id.required' => 'El campo worker_id es obligatorio.',
            'worker_id.exists' => 'El worker_id debe existir en la tabla workers.',
            'product_id.required_if' => 'El campo product_id es obligatorio cuando el tipo es Producto.',
            'product_id.exists' => 'El product_id debe existir en la tabla products.',
            'service_id.required_if' => 'El campo service_id es obligatorio cuando el tipo es Servicio.',
            'service_id.exists' => 'El service_id debe existir en la tabla services.',
        ];
    }
}
