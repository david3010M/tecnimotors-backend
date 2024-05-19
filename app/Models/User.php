<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @OA\Schema (
 * schema="User",
 *     title="User",
 *     type="object",
 *     required={"id", "username", "password", "worker_id", "typeofUser_id"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="username", type="string", example="johndoe"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(
 *         property="worker_id",
 *         type="number",
 *         example="1",
 *         description="Foreign key referencing Worker"
 *     ),
 *     @OA\Property(
 *         property="typeofUser_id",
 *         type="number",
 *         example="1",
 *         description="Foreign key referencing TypeOfUser"
 *     ),
 *     @OA\Property(
 *         property="worker",
 *         ref="#/components/schemas/Worker"
 *     ),
 *     @OA\Property(
 *         property="TypeUser",
 *         ref="#/components/schemas/TypeUser"
 *     ),
 * )
 */
class User  extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;


    protected $fillable = [
        'id',
        'username',
        'password',

        'created_at',
        'worker_id',
        'typeofUser_id',
    ];

    protected $hidden = [
        'password',
        'updated_at',
        'deleted_at',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
    public function typeUser()
    {
        return $this->belongsTo(Worker::class, 'typeUser_id');
    }
}
