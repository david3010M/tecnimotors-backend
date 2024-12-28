<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ConceptMov",
 *     title="ConceptMov",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="ConceptMov 1"),
 *     @OA\Property(property="typemov", type="string", example="INGRESO"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22 00:43:09")
 * )
 *
 * @OA\Schema(
 *     schema="ConceptMovRequest",
 *     title="ConceptMovRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="ConceptMov 1")
 * )
 */
class ConceptMov extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'concept_movs';

    protected $fillable = [
        'name',
        'typemov',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

}
