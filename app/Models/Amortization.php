<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 *     schema="Amortization",
 *     title="Amortization",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="sequentialNumber", type="string", example="123456"),
 *     @OA\Property(property="amount", type="number", example="100.00"),
 *     @OA\Property(property="paymentDate", type="string", example="2024-06-27 22:59:36"),
 *     @OA\Property(property="moviment_id", type="integer", example="1"),
 *     @OA\Property(property="commitment_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", example="2024-06-27 22:59:36")
 * )
 *
 *
 * @OA\Schema(
 *     schema="AmortizationRequest",
 *     title="AmortizationRequest",
 *     required={"paymentDate", "isBankPayment", "person_id", "budgetSheet_id", "commitment_id"},
 *     @OA\Property(property="paymentDate", type="string", example="2024-06-27 22:59:36"),
 *     @OA\Property(property="routeVoucher", type="file", format="binary"),
 *     @OA\Property(property="numberVoucher", type="string", example="123456"),
 *     @OA\Property(property="yape", type="number", example="0.00"),
 *     @OA\Property(property="deposit", type="number", example="10.00"),
 *     @OA\Property(property="cash", type="number", example="10.00"),
 *     @OA\Property(property="plin", type="number", example="0.00"),
 *     @OA\Property(property="card", type="number", example="0.00"),
 *     @OA\Property(property="comment", type="string", example="comment"),
 *     @OA\Property(property="isBankPayment", type="integer", example="1"),
 *     @OA\Property(property="bank_id", type="integer", example="1"),
 *     @OA\Property(property="person_id", type="integer", example="3"),
 *     @OA\Property(property="commitment_id", type="integer", example="1")
 * )
 *
 */
class Amortization extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'sequentialNumber',
        'amount',
//        'status',
        'paymentDate',
        'moviment_id',
        'commitment_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];


    protected $hidden = ['updated_at', 'deleted_at'];

    public function moviment()
    {
        return $this->belongsTo(Moviment::class, 'moviment_id');
    }

    public function commitment()
    {
        return $this->belongsTo(Commitment::class, 'commitment_id');
    }


}
