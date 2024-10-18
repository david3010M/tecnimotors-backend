<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     title="Branch",
 *     description="Branch model",
 *     @OA\Property ( property="id", type="integer", example="1" ),
 *     @OA\Property ( property="name", type="string", example="Tecnimotors del Perú" ),
 *     @OA\Property ( property="created_at", type="string", format="date-time", example="2024-10-18 12:39:12" )
 * )
 */
class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function cashes()
    {
        return $this->hasMany(Cash::class);
    }
}
