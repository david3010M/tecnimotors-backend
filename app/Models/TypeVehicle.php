<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="TypeVehicle",
 *     type="object",
 *     title="TypeVehicle",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Carro"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-05-21 03:02:20")
 * )
 */
class TypeVehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'type_vehicles';

    protected $fillable = [
        'name',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

}
