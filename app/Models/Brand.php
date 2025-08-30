<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema (
 *     schema="Brand",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Brand 1"),
 *     @OA\Property(property="type", type="string", example="Type 1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41")
 * )
 */
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'type',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    const filters = [
        'name' => 'like',
        'type' => 'like',
        'created_at' => 'between',
    ];


    const sorts = [
        'id',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    //    public function products()
//    {
//        return $this->hasMany(Product::class);
//    }

}
