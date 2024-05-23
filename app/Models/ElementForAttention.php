<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="ElementForAttention",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="element_id", type="integer", example="Element 1"),
 *      @OA\Property(property="attention_id", type="integer", example="Element 1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25"),
 *       @OA\Property(
 *         property="element",
 *         ref="#/components/schemas/Element"
 *     ),
 *@OA\Property(
 *         property="attention",
 *         ref="#/components/schemas/Attention"
 *     ),
 * )
 */

class ElementForAttention extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'element_for_attentions';

    protected $fillable = [
        'element_id',
        'attention_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function element()
    {
        return $this->belongsTo(Person::class, 'element_id');
    }
    public function attention()
    {
        return $this->belongsTo(Person::class, 'attention_id');
    }
}
