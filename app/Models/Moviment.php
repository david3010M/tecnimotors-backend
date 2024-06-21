<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moviment extends Model
{
    use SoftDeletes;
    /**
     * @OA\Schema(
     *     schema="MovimentRequest",
     *     title="moviment",
     *     description="Movimiento",
     *     required={"id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="ID del moviment"
     *     ),
     *     @OA\Property(
     *         property="sequentialNumber",
     *         type="string",
     *         description="Número secuencial",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="paymentDate",
     *         type="string",
     *         format="date-time",
     *         description="Fecha de pago",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="total",
     *         type="number",
     *         format="decimal",
     *         description="Total",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="yape",
     *         type="number",
     *         format="decimal",
     *         description="Pago por Yape",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="deposit",
     *         type="number",
     *         format="decimal",
     *         description="Depósito",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="cash",
     *         type="number",
     *         format="decimal",
     *         description="Efectivo",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="card",
     *         type="number",
     *         format="decimal",
     *         description="Pago por tarjeta",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="plin",
     *         type="number",
     *         format="decimal",
     *         description="Pago por tarjeta",
     *         nullable=true
     *     ),

     *     @OA\Property(
     *         property="typeDocument",
     *         type="string",
     *         description="Tipo de documento",
     *         nullable=true
     *     ),
     *        @OA\Property(
     *         property="isBankPayment",
     *         type="boolean",
     *         description="Tipo de documento",
     *         nullable=true
     *     ),
     *        @OA\Property(
     *         property="numberVoucher",
     *         type="string",
     *         description="Tipo de documento",
     *         nullable=true
     *     ),
     *        @OA\Property(
     *         property="routeVoucher",
     *         type="string",
     *         description="Tipo de documento",
     *         nullable=true
     *     ),
     *
     *     @OA\Property(
     *         property="comment",
     *         type="string",
     *         description="Comentario",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="status",
     *         type="string",
     *         description="Estado",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="conceptMov_id",
     *         type="integer",
     *         description="ID del concepto de movimiento",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         description="ID del usuario",
     *         nullable=true
     *     ),

     *     @OA\Property(
     *         property="person_id",
     *         type="integer",
     *         description="ID de persona",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="bank_id",
     *         type="integer",
     *         description="ID del banco",
     *         nullable=true
     *     )
     * );
     */

    protected $fillable = [
        'sequentialNumber',
        'paymentDate',
        'total',
        'yape',
        'deposit',
        'cash',
        'card',
        'plin',

        'typeDocument',
        'isBankPayment',
        'numberVoucher',
        'routeVoucher',
        'comment',
        'status',

        'created_at',

        'person_id',
        'user_id',
        'bank_id',
        'paymentConcept_id',
        'budgetSheet_id',
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function paymentConcept()
    {
        return $this->belongsTo(ConceptPay::class, 'paymentConcept_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    public function budgetSheet()
    {
        return $this->belongsTo(budgetSheet::class, 'budgetSheet_id');
    }

}
