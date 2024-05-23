<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Service",
 *     title="Service",
 *     required={"name", "quantity", "saleprice", "time"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Service 1"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="saleprice", type="number", format="float", example="100.00"),
 *     @OA\Property(property="time", type="number", format="float", example="2.5"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22 00:41:38")
 * )
 *
 * @OA\Schema(
 *     schema="ServiceRequest",
 *     title="ServiceRequest",
 *     required={"name", "quantity", "saleprice", "time"},
 *     @OA\Property(property="name", type="string", example="Service 1"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="saleprice", type="number", format="float", example="100.00"),
 *     @OA\Property(property="time", type="number", format="float", example="2.5")
 * )
 */
class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'quantity',
        'saleprice',
        'time',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

//    public function detailBudgets()
//    {
//        return $this->hasMany(DetailBudget::class);
//    }


}
