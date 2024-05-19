<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="TypeUser",
 *     title="TypeUser",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Administrador"),
 *
 * )
 */
class TypeUser extends Model
{
    use SoftDeletes;

    protected $table = 'typeuser';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function access()
    {
        return $this->hasMany(Access::class, 'typeuser_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'typeuser_id');
    }
}
