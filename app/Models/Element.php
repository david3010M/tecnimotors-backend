<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Element",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Element 1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25")
 * )
 */
class Element extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'elements';

    protected $fillable = [
        'name',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

//    public function elementsForAttention()
//    {
//        return $this->hasMany(ElementForAttention::class);
//    }
}
