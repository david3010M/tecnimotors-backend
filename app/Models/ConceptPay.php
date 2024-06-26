<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ConceptPay",
 *     title="ConceptPay",
 *     required={"number", "name", "type"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="ConceptPay 1"),
 *     @OA\Property(property="type", type="string", example="Ingreso"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22 00:41:38")
 * )
 *
 * @OA\Schema(
 *     schema="ConceptPayRequest",
 *     title="ConceptPayRequest",
 *     required={"name", "type"},
 *     @OA\Property(property="name", type="string", example="ConceptPay 1"),
 *     @OA\Property(property="type", type="string", example="Ingreso")
 * )
 */
class ConceptPay extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'name',
        'type',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
}
