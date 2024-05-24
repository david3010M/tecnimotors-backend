<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialtyPerson extends Model
{
    /**
     * @OA\Schema(
     *     schema="SpecialtyPerson",
     *     title="SpecialtyPerson",
     *     type="object",
     *     @OA\Property(property="id", type="number", example="1"),
     *     @OA\Property(property="name", type="string", example="Especialidad"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     * )
     */

    protected $fillable = [
        'specialty_id',
        'worker_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',

    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

}
