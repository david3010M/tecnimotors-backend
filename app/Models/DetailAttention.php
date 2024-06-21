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
 *     @OA\Property(property="comment", type="string", example="comment"),
 *     @OA\Property(property="status", type="string", example="Generada"),
 *     @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *     @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 *     @OA\Property(property="dateCurrent", type="string", format="date", example="2024-04-24"),
 *     @OA\Property(property="percentage", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41"),
 *     @OA\Property(property="attention_id", type="integer", example="Attention 1"),
 *     @OA\Property(property="service_id", type="integer", example="service 1"),
 *     @OA\Property(property="product_id", type="integer", example="product 1"),
 *     @OA\Property(property="worker_id", type="integer", example="worker 1"),
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
 *      schema="DetailAttentionNoRelations",
 *      type="object",
 *      required={"salePrice","attention_id"},
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="salePrice", type="decimal", example="0.00"),
 *      @OA\Property(property="quantity", type="integer", example="1"),
 *      @OA\Property(property="type", type="string", example="Producto"),
 *      @OA\Property(property="comment", type="string", example="comment"),
 *      @OA\Property(property="status", type="string", example="Generada"),
 *      @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="dateCurrent", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="percentage", type="integer", example="1"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41"),
 *      @OA\Property(property="attention_id", type="integer", example="Attention 1"),
 *      @OA\Property(property="service_id", type="integer", example="service 1"),
 *      @OA\Property(property="product_id", type="integer", example="product 1"),
 *      @OA\Property(property="worker_id", type="integer", example="worker 1")
 *  )
 *
 * @OA\Schema(
 *     schema="DetailAttentionRequestUpdate",
 *     type="object",
 *     required={"salePrice","attention_id"},
 *     @OA\Property(property="salePrice", type="decimal", example="100.00"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 * )
 * @OA\Schema(
 *     schema="DetailAttentionRequest",
 *     type="object",
 *     required={"salePrice","quantity"},
 *     @OA\Property(property="salePrice", type="decimal", example="0.00"),
 *     @OA\Property(property="type", type="string", example="Producto"),
 *     @OA\Property(property="comment", type="string", example="comment"),
 *     @OA\Property(property="attention_id", type="integer", example="Attention 1"),
 *     @OA\Property(property="service_id", type="integer", example="service 1"),
 *     @OA\Property(property="product_id", type="integer", example="product 1"),
 *     @OA\Property(property="worker_id", type="integer", example="worker 1"),
 *     @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *     @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 * )
 *
 * @OA\Schema(
 *      schema="DetailAttentionService",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="salePrice", type="decimal", example="0.00"),
 *      @OA\Property(property="quantity", type="integer", example="1"),
 *      @OA\Property(property="type", type="string", example="Producto"),
 *      @OA\Property(property="comment", type="string", example="comment"),
 *      @OA\Property(property="status", type="string", example="Generada"),
 *      @OA\Property(property="dateRegister", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="dateMax", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="dateCurrent", type="string", format="date", example="2024-04-24"),
 *      @OA\Property(property="percentage", type="integer", example="1"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-24 12:27:41"),
 *      @OA\Property(property="service_id", type="integer", example="service 1"),
 *      @OA\Property(property="service", type="object", ref="#/components/schemas/Service")
 *  )
 *
 * @OA\Schema(
 *      schema="DetailAttentionServicePaginate",
 *      type="object",
 *      @OA\Property(property="current_page", type="integer", example="1"),
 *      @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DetailAttentionService")),
 *      @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/detailAttention?page=1"),
 *      @OA\Property(property="from", type="integer", example="1"),
 *      @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/detailAttention?page=2"),
 *      @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/detailAttention"),
 *      @OA\Property(property="per_page", type="integer", example="15"),
 *      @OA\Property(property="prev_page_url", type="string", example="null"),
 *      @OA\Property(property="to", type="integer", example="15")
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

    public static function updatePercentage($detailAttentionId)
    {
        $totalTasks = Task::where('detail_attentions_id', $detailAttentionId)->count();
        $completedTasks = Task::where('detail_attentions_id', $detailAttentionId)->where('status', 'listo')->count();
        $percentage = ($completedTasks / $totalTasks) * 100;

        $detailAttention = DetailAttention::find($detailAttentionId);
        $detailAttention->percentage = $percentage;
        $detailAttention->save();
    }

    public static function updateStatus($detailAttentionId)
    {
        $taskDoing = Task::where('detail_attentions_id', $detailAttentionId)->where('status', 'hacer')->count();
        $taskCourse = Task::where('detail_attentions_id', $detailAttentionId)->where('status', 'curso')->count();
        $taskReady = Task::where('detail_attentions_id', $detailAttentionId)->where('status', 'listo')->count();
        $totalTasks = Task::where('detail_attentions_id', $detailAttentionId)->count();

        $detailAttention = DetailAttention::find($detailAttentionId);

        if ($totalTasks == 0) {
            $detailAttention->status = 'Generada';
        } else if ($totalTasks == $taskReady) {
            $detailAttention->status = 'Lista';
        } else if ($taskCourse > 0) {
            $detailAttention->status = 'Curso';
        } else if ($taskDoing > 0) {
            $detailAttention->status = 'Iniciada';
        }

        $detailAttention->save();
    }

}
