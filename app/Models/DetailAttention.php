<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="DetailAttention",
 *     type="object",
 *     required={"salePrice","attention_id"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="salePrice", type="decimal", example="0.00"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="type", type="string", example="Producto"),
 *      @OA\Property(property="comment", type="string", example="comment"),
 *      @OA\Property(property="status", type="string", example="Generada"),
 *  @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *  @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 *  @OA\Property(property="dateCurrent", type="string", format="date", example="2024-04-24"),
 *     @OA\Property(property="percentage", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41"),
 * @OA\Property(property="attention_id", type="integer", example="Attention 1"),
 * @OA\Property(property="service_id", type="integer", example="service 1"),
 * @OA\Property(property="product_id", type="integer", example="product 1"),
 * @OA\Property(property="worker_id", type="integer", example="worker 1"),
 *       @OA\Property(
 *         property="attention",
 *         ref="#/components/schemas/Attention"
 *     ),
 *         @OA\Property(
 *         property="service",
 *         ref="#/components/schemas/Service"
 *     ),
 *         @OA\Property(
 *         property="product",
 *         ref="#/components/schemas/Product"
 *     ),
 *         @OA\Property(
 *         property="worker",
 *         ref="#/components/schemas/Worker"
 *     ),
 * )
 *
 * @OA\Schema(
 *     schema="DetailAttentionRequest",
 *     type="object",
 *     required={"salePrice","attention_id"},
 *      @OA\Property(property="salePrice", type="decimal", example="0.00"),
 *     @OA\Property(property="type", type="string", example="Producto"),
 *      @OA\Property(property="comment", type="string", example="comment"),
 *   @OA\Property(property="attention_id", type="integer", example="Attention 1"),
 * @OA\Property(property="service_id", type="integer", example="service 1"),
 * @OA\Property(property="product_id", type="integer", example="product 1"),
 * @OA\Property(property="worker_id", type="integer", example="worker 1"),
 *  @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *  @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 * )
 *
 */
class DetailAttention extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'saleprice',
        'quantity',
        'type',
        'comment',
        'status',
        'dateRegister',
        'dateMax',
        'dateCurrent',
        'percentage',
        'service_id',
        'product_id',
        'worker_id',
        'attention_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function attention()
    {
        return $this->belongsTo(Attention::class, 'attention_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function task()
    {
        return $this->hasMany(Task::class);
    }

}
