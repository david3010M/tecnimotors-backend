<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Category 1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Category 1")
 * )
 *
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

//    public function products()
//    {
//        return $this->hasMany(Product::class);
//    }
}
