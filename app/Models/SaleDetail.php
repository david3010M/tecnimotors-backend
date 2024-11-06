<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'unit',
        'quantity',
        'unitValue',
        'unitPrice',
        'discount',
        'subTotal',
        'sale_id',
        'note_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unitValue' => 'decimal:2',
        'unitPrice' => 'decimal:2',
        'discount' => 'decimal:2',
        'subTotal' => 'decimal:2',
    ];

    const filters = [
        'description' => 'like',
        'unit' => 'like',
        'quantity' => '=',
        'unitValue' => '=',
        'unitPrice' => '=',
        'discount' => '=',
        'subTotal' => '=',
        'sale_id' => '=',
    ];

    const sorts = [
        'id',
        'description',
        'unit',
        'quantity',
        'unitValue',
        'unitPrice',
        'discount',
        'subTotal',
        'sale_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
