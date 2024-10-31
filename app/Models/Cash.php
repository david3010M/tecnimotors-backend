<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     title="Cash",
 *     description="Cash model",
 *     @OA\Property( property="id", type="integer", example="1" ),
 *     @OA\Property( property="name", type="string", example="Caja 1" ),
 *     @OA\Property( property="branch_id", type="integer", example="1" ),
 *     @OA\Property( property="created_at", type="string", format="date-time", example="2021-09-01 00:00:00" )
 * )
 */
class Cash extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'series', 'branch_id', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
