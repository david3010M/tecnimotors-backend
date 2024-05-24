<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{

/**
 * @OA\Schema(
 *     schema="Specialty",
 *     title="Specialty",
 *     type="object",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Especialidad"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 * )
 */

    protected $fillable = [
        'name',
        'created_at',
    ];

    protected $hidden = [
        'pivot',
        'updated_at',
        'deleted_at',
    ];

    public function specialtyPerson()
    {
        return $this->hasMany(SpecialtyPerson::class, 'specialtyPerson_id');
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'specialty_people');
    }
}
