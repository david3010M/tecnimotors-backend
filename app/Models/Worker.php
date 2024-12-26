<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 * schema="Worker",
 *     title="Worker",
 *     type="object",
 *     required={"id", "occupation", "person_id"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="startDate", type="string", format="date", example="2023-01-01"),
 *     @OA\Property(property="birthDate", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="occupation", type="string", example="Engineer"),
 *     @OA\Property(
 *         property="person_id",
 *         type="number",
 *         example="1",
 *         description="Foreign key referencing Person"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(
 *         property="person",
 *         ref="#/components/schemas/Person"
 *     ),
 * )
 */
class Worker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'startDate',
        'birthDate',
        'occupation',
        'ocupation_id',
        'state',
        'person_id',
        'created_at',

    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function ocupation()
    {
        return $this->belongsTo(Ocupation::class, 'ocupation_id');
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'specialty_people');
    }

    public function guides()
    {
        return $this->hasMany(Guide::class);
    }
    public function attentions()
    {
        return $this->hasMany(Attention::class);
    }
}
