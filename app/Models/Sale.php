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
        'fullNumber',
        'paymentDate',
        'documentType',
        'saleType',
        'detractionCode',
        'detractionPercentage',
        'paymentType',
        'status',
        'status_facturado',
        'taxableOperation',
        'igv',
        'total',
        'yape',
        'deposit',
        'nro_operation',
        'effective',
        'card',
        'plin',
        'isBankPayment',
        'bank_id',
        'numberVoucher',
        'routeVoucher',
        'comment',
        'person_id',
        'budget_sheet_id',
        'cash_id',
        'user_id',
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
        'from' => '>=',
        'to' => '<=',
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
        return $this->belongsTo(budgetSheet::class);
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

    public static function getSales(
        $number = null,
        $from = null,
        $to = null,
        $documentType = null,
        $saleType = null,
        $paymentType = null,
        $status = null,
        $person_id = null,
        $personDocumentNumber = null,
        $budget_sheet_id = null
    )
    {
        $query = Sale::with([
            'person',
            'budgetSheet',
            'commitments',
            'saleDetails',
            'moviment',
            'bank',
            'cash',
            'user',
        ]);

        if ($number) {
            $query->where('number', 'like', "%$number%");
        }
        if ($from) {
            $query->where('paymentDate', '>=', $from);
        }
        if ($to) {
            $query->where('paymentDate', '<=', $to);
        }
        if ($documentType) {
            $query->where('documentType', 'like', "%$documentType%");
        }
        if ($saleType) {
            $query->where('saleType', 'like', "%$saleType%");
        }
        if ($paymentType) {
            $query->where('paymentType', 'like', "%$paymentType%");
        }
        if ($status) {
            $query->where('status', 'like', "%$status%");
        }
        if ($person_id) {
            $query->where('person_id', '=', $person_id);
        }
        if ($personDocumentNumber) {
            $query->whereHas('person', function ($query) use ($personDocumentNumber) {
                $query->where('documentNumber', 'like', "%$personDocumentNumber%");
            });
        }
        if ($budget_sheet_id) {
            $query->where('budget_sheet_id', '=', $budget_sheet_id);
        }

        return $query->orderBy('paymentDate', 'desc')->get();
    }


}
