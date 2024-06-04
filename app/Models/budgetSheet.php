<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="BudgetSheet",
 * title="BudgetSheet",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="number", type="string", example="PRES-00000001"),
 *     @OA\Property(property="paymentType", type="string", example="Al Contado"),
 *     @OA\Property(property="totalService", type="number", format="float", example=0.00),
 *     @OA\Property(property="totalProducts", type="number", format="float", example=0.00),
 *     @OA\Property(property="debtAmount", type="number", format="float", example=0.00),
 *     @OA\Property(property="total", type="number", format="float", example=0.00),
 *     @OA\Property(property="discount", type="number", format="float", example=0.00),
 *     @OA\Property(property="subtotal", type="number", format="float", example=0.00),
 *     @OA\Property(property="igv", type="number", format="float", example=0.00),
 *     @OA\Property(
 *         property="Attention",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Attention")
 *     )
 * )
 */

class budgetSheet extends Model
{
    protected $fillable = [
        'number',
        'paymentType',
        'totalService',
        'totalProducts',
        'debtAmount',
        'total',
        'discount',
        'subtotal',
        'igv',
        'igv',
        'attention_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function attention()
    {
        return $this->belongsTo(Attention::class, 'attention_id');
    }
}
