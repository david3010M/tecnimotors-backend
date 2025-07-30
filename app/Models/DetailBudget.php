<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailBudget extends Model
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
        'period',
        'budget_sheet_id',
        'worker_id',
        'service_id',
        'product_id',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    const filters = [
        'saleprice' => '=',
        'quantity' => '=',
        'type' => '=',
        'comment' => 'like',
        'status' => '=',
        'dateRegister' => 'date',
        'dateMax' => 'date',
        'dateCurrent' => 'date',
        'percentage' => '=',
        'period' => '=',
        'budget_sheet_id' => '=',
        'worker_id' => '=',
        'service_id' => '=',
        'product_id' => '=',
        'created_at' => 'date'
    ];

    const sorts = [
        'id' => 'desc'
    ];

    public function budget_sheet()
    {
        return $this->belongsTo(budgetSheet::class, 'budget_sheet_id');
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