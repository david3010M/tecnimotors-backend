<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Supplier",
 *     title="Supplier",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="date", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="category", type="string", example="Category 1"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22T00:00:00.000000Z"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person"),
 * )
 *
 * @OA\Schema(
 *     schema="SupplierRequest",
 *     title="SupplierRequest",
 *     required={"date", "category", "person_id"},
 *     @OA\Property(property="date", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="category", type="string", example="Category 1"),
 *     @OA\Property(property="person_id", type="integer", example="1"),
 * )
 */
class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',
        'category',
        'person_id',
        'created_at',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
