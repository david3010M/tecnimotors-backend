<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commitment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dues',
        'amount',
        'balance',
        'payment_date',
        'payment_method',
        'status',
        'budget_sheet_id',
        'created_at',
    ];

    protected $casts = [
        'payment_date' => 'datetime'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function budgetSheet()
    {
        return $this->belongsTo(BudgetSheet::class);
    }

}
