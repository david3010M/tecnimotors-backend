<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'status',
        'percentage',
        'dateRegister',
        'dateStart',
        'dateEnd',
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

}
