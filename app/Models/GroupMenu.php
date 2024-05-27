<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     title="GroupMenu",
 *     type="object",
 *     required={"id","name", "icon"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="icon", type="string", example="fas fa-user"),
 *     @OA\Property(property="created_at", type="string", example="2024-03-27 01:42:21"),
 *   @OA\Property(
 *         property="option_menus",
 *         ref="#/components/schemas/OptionMenu"
 *     ),
 * )
 */
class GroupMenu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'icon',
        'created_at',

    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function optionMenus()
    {
        return $this->hasMany(Optionmenu::class, 'groupmenu_id');
    }
}
