<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="VehicleModel",
 *     title="VehicleModel",
 *     required={"name", "brand_id"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Gol"),
 *     @OA\Property(property="brand_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-08-01T00:00:00")
 * )
 *
 * @OA\Schema (
 *     schema="VehicleModelRequest",
 *     title="VehicleModelRequest",
 *     required={"name", "brand_id"},
 *     @OA\Property(property="name", type="string", example="Gol"),
 *     @OA\Property(property="brand_id", type="integer", example="1")
 * )
 *
 */
class VehicleModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'brand_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

}
