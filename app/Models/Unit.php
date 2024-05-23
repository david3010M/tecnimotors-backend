<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Unit",
 *     title="Unit",
 *     required={"name", "code"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Kilogram"),
 *     @OA\Property(property="code", type="string", example="kg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-14T17:59:10.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="UnitRequest",
 *     title="UnitRequest",
 *     required={"name", "code"},
 *     @OA\Property(property="name", type="string", example="Kilogram"),
 *     @OA\Property(property="code", type="string", example="kg")
 * )
 */
class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

//    public function products()
//    {
//        return $this->hasMany(Product::class, 'unit_id');
//    }
}
