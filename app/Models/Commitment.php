<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * REQUEST SCHEMA
 * @OA\Schema(
 *     schema="CommitmentRequest",
 *     title="CommitmentRequest",
 *     required={"amount", "dues"},
 *     @OA\Property(property="amount", type="decimal", example="1000.00"),
 *     @OA\Property(property="dues", type="integer", example="10"),
 *     @OA\Property(property="payment_date", type="string", example="2024-06-27 22:59:36"),
 *     @OA\Property(property="payment_type", type="string", example="Semanal"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1")
 * )
 *
 *
 * @OA\Schema(
 *     schema="Commitment",
 *     title="Commitment",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="dues", type="integer", example="10"),
 *     @OA\Property(property="amount", type="decimal", example="1000.00"),
 *     @OA\Property(property="balance", type="decimal", example="900.00"),
 *     @OA\Property(property="payment_date", type="string", example="2024-06-27 22:59:36"),
 *     @OA\Property(property="payment_type", type="string", example="Semanal"),
 *     @OA\Property(property="status", type="string", example="Pendiente"),
 *     @OA\Property(property="budget_sheet_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", example="2024-06-27 22:59:36")
 * )
 *
 */
class Commitment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dues',
        'payment_pending',
        'amount',
        'balance',
        'payment_date',
        'payment_type',
        'status',
        'budget_sheet_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function budgetSheet()
    {
        return $this->belongsTo(budgetSheet::class);
    }

}
