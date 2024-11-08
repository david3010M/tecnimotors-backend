<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'fullNumber',
        'documentType',
        'date',
        'comment',
        'company',
        'discount',
        'totalCreditNote',
        'totalDocumentReference',
        'note_reason_id',
        'sale_id',
        'status',
        'user_id',
        'cash_id',
        'created_at',
    ];

    const filters = [
        'number' => 'like',
        'date' => 'between',
        'documentType' => 'like',
        'company' => 'like',
        'discount' => 'like',
        'totalCreditNote' => 'like',
        'totalDocumentReference' => 'like',
        'note_reason_id' => 'like',
        'sale_id' => 'like',
    ];

    const sorts = [
        'number',
        'date',
        'documentType',
        'company',
        'discount',
        'totalCreditNote',
        'totalDocumentReference',
        'note_reason_id',
        'sale_id',
        'cash_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];


    public function noteReason()
    {
        return $this->belongsTo(NoteReason::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

}
