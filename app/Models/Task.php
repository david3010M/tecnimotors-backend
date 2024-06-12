<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Task",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="description", type="string", example="Task 1"),
 *     @OA\Property(property="status", type="string", example="Task 1"),
 *     @OA\Property(property="registerDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="limitDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="worker_id", type="integer", example="1"),
 *     @OA\Property(property="detail_attentions_id", type="integer", example="1"),
 *     @OA\Property(
 *         property="worker",
 *         ref="#/components/schemas/Worker"
 *     ),
 *     @OA\Property(
 *         property="detailAttentions",
 *         ref="#/components/schemas/DetailAttention"
 *     ),
 * )
 *
 * @OA\Schema (
 *     schema="TaskNoRelations",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="description", type="string", example="Task 1"),
 *     @OA\Property(property="status", type="string", example="Task 1"),
 *     @OA\Property(property="registerDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="limitDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="worker_id", type="integer", example="1"),
 *     @OA\Property(property="detail_attentions_id", type="integer", example="1"),
 * )
 *
 * @OA\Schema (
 *     schema="TaskRequest",
 *     type="object",
 *     required={"description", "detail_attentions_id"},
 *     @OA\Property(property="description", type="string", example="Task 1"),
 *     @OA\Property(property="registerDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="limitDate", type="string", format="date-time", example="2024-05-21"),
 *     @OA\Property(property="detail_attentions_id", type="integer", example="1"),
 * )
 *
 * @OA\Schema (
 *     schema="TaskUpdate",
 *     type="object",
 *     @OA\Property(property="description", type="string", example="Task 1"),
 *     @OA\Property(property="status", type="string", enum={"hacer", "curso", "listo"}, example="hacer"),
 *     @OA\Property(property="limitDate", type="string", format="date-time", example="2024-05-21")
 *
 * )
 *
 * @OA\Schema (
 *     schema="TaskPaginate",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example="1"),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
 *     @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/v1/tasks?page=1"),
 *     @OA\Property(property="from", type="integer", example="1"),
 *     @OA\Property(property="last_page", type="integer", example="1"),
 *     @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/v1/tasks?page=1"),
 *     @OA\Property(property="next_page_url", type="string", example="http://localhost:8000/api/v1/tasks?page=1"),
 *     @OA\Property(property="path", type="string", example="http://localhost:8000/api/v1/tasks"),
 *     @OA\Property(property="per_page", type="integer", example="15"),
 *     @OA\Property(property="prev_page_url", type="string", example="http://localhost:8000/api/v1/tasks?page=1"),
 *     @OA\Property(property="to", type="integer", example="1"),
 *     @OA\Property(property="total", type="integer", example="1"),
 * )
 *
 * @OA\Schema (
 *     schema="TaskImages",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="url", type="string", example="http://localhost:8000/storage/tasks/1/image.jpg"),
 *     @OA\Property(property="task_id", type="integer", example="1"),
 * )
 *
 * @OA\Schema (
 *     schema="TaskImagesRequest",
 *     type="object",
 *     required={"image"},
 *     @OA\Property(
 *          description="Evidence of the task",
 *          property="image",
 *          type="string", format="binary"
 *     )
 * )
 *
 */
class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'status',
//        'percentage',
        'registerDate',
        'limitDate',
//        'dateStart',
//        'dateEnd',
        'worker_id',
        'detail_attentions_id'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function detailAttentions()
    {
        return $this->belongsTo(DetailAttention::class);
    }

    public function routeImages()
    {
        return $this->hasMany(RouteImages::class);
    }

}
