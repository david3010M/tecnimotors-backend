<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'paymentDate',
        'documentType',
        'saleType',
        'detractionCode',
        'detractionPercentage',
        'paymentType',
        'status',
        'taxableOperation',
        'igv',
        'total',
        'yape',
        'deposit',
        'effective',
        'card',
        'plin',
        'isBankPayment',
        'numberVoucher',
        'routeVoucher',
        'comment',
        'person_id',
        'budget_sheet_id',
        'cash_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'paymentDate' => 'date:Y-m-d',
    ];

    const filters = [
        'number' => 'like',
        'paymentDate' => 'between',
        'documentType' => 'like',
        'saleType' => 'like',
        'detractionCode' => 'like',
        'detractionPercentage' => 'like',
        'paymentType' => 'like',
        'status' => 'like',
        'person_id' => '=',
        'person.documentNumber' => 'like',
        'budget_sheet_id' => '=',
    ];

    const sorts = [
        'id',
        'number',
        'paymentDate',
        'documentType',
        'saleType',
        'detractionCode',
        'detractionPercentage',
        'paymentType',
        'status',
        'taxableOperation',
        'igv',
        'total',
        'yape',
        'deposit',
        'effective',
        'card',
        'plin',
        'isBankPayment',
        'numberVoucher',
        'routeVoucher',
        'comment',
        'person_id',
        'budget_sheet_id',
        'bank_id',
        'cash_id',
        'user_id',
        'created_at',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function budgetSheet()
    {
        return $this->belongsTo(BudgetSheet::class);
    }

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function moviment()
    {
        return $this->hasOne(Moviment::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
