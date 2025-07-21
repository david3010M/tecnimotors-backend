<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="RouteImages",
 *     title="RouteImages",
 *     description="Route Image model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="route", type="string", example="Image route"),
 *     @OA\Property(property="attention_id", type="integer", example="1"),
 *     @OA\Property(property="task_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 * )
 *
 */
class RouteImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'route',
        'attention_id',
        'product_id',
        'task_id',
        'concession_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function attention()
    {
        return $this->belongsTo(Attention::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function concession()
    {
        return $this->belongsTo(Concession::class);
    }
     public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
