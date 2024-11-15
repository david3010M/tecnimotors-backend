<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'unit',
        'quantity',
        'weight',
        'status',
        'guide_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    const STATUS_PENDING = 'PENDIENTE';

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
