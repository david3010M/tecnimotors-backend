<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Ocupation",
 *     title="Ocupation",
 *     required={"name", "comment"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Kilogram"),
 *     @OA\Property(property="comment", type="string", example="kg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-14T17:59:10.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="OcupationRequest",
 *     title="OcupationRequest",
 *     required={"name", "comment"},
 *     @OA\Property(property="name", type="string", example="Kilogram"),
 *     @OA\Property(property="comment", type="string", example="kg")
 * )
 */

class Ocupation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'comment',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
}
