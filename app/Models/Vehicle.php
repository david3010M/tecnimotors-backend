<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *      schema="Vehicle",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="plate", type="string", example="ABC123"),
 *      @OA\Property(property="km", type="number", example="15000"),
 *      @OA\Property(property="year", type="integer", example="2020"),
 *      @OA\Property(property="model", type="string", example="Model 1"),
 *      @OA\Property(property="chasis", type="string", example="Chasis 1"),
 *      @OA\Property(property="motor", type="string", example="Motor 1"),
 *      @OA\Property(property="person_id", type="integer", example="1"),
 *      @OA\Property(property="typeVehicle_id", type="integer", example="1"),
 *      @OA\Property(property="brand_id", type="integer", example="1"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2021-06-15 09:00:00")
 *  )
 *
 *
 * @OA\Schema (
 *     schema="VehicleCollection",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="plate", type="string", example="ABC123"),
 *     @OA\Property(property="km", type="number", example="15000"),
 *     @OA\Property(property="year", type="integer", example="2020"),
 *     @OA\Property(property="model", type="string", example="Model 1"),
 *     @OA\Property(property="chasis", type="string", example="Chasis 1"),
 *     @OA\Property(property="motor", type="string", example="Motor 1"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="typeVehicle_id", type="integer", example="1"),
 *     @OA\Property(property="brand_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-06-15 09:00:00"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person"),
 *     @OA\Property(property="type_vehicle", ref="#/components/schemas/TypeVehicle"),
 *     @OA\Property(property="brand", ref="#/components/schemas/Brand")
 * )
 *
 * @OA\Schema (
 *      schema="VehicleRequest",
 *      type="object",
 *      @OA\Property(property="plate", type="string", example="ABC123"),
 *      @OA\Property(property="km", type="number", example="15000"),
 *      @OA\Property(property="year", type="integer", example="2020"),
 *      @OA\Property(property="model", type="string", example="Model 1"),
 *      @OA\Property(property="chasis", type="string", example="Chasis 1"),
 *      @OA\Property(property="motor", type="string", example="Motor 1"),
 *      @OA\Property(property="person_id", type="integer", example="1"),
 *      @OA\Property(property="typeVehicle_id", type="integer", example="1"),
 *      @OA\Property(property="brand_id", type="integer", example="1"),
 *  )
 */
class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'plate',
        'km',
        'year',
        'model',
        'chasis',
        'motor',
        'person_id',
        'typeVehicle_id',
        'brand_id',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function typeVehicle()
    {
        return $this->belongsTo(TypeVehicle::class, 'typeVehicle_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

//    public function attentions()
//    {
//        return $this->hasMany(Attention::class);
//    }
}
