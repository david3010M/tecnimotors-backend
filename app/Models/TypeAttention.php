<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="TypeAttention",
 *     type="object",
 *     title="TypeAttention",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Consulta"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-05-21 03:02:20")
 * )
 */
class TypeAttention extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'type_attentions';

    protected $fillable = [
        'name',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

//    public function attentions()
//    {
//        return $this->hasMany(Attention::class);
//    }
}
