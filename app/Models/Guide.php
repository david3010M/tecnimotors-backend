<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'full_number',
        'date_emision',
        'date_traslado',
        'motive_name',
        'modality',
        'recipient_names',
        'recipient_document',
        'driver_names',
        'driver_surnames',
        'driver_document',
        'vehicle_placa',
        'driver_licencia',
        'nro_paquetes',
        'transbordo',
        'cod_motive',
        'net_weight',
        'ubigeo_end',
        'address_end',
        'ubigeo_start',
        'address_start',
        'observation',
        'factura',
        'status_facturado',
        'user_id',
        'branch_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    const filters = [];
    const sorts = [];

    const motives = [
        'VENTA',
        'COMPRA',
    ];

    const modalities = [
        'TRANSPORTE PÚBLICO',
        'TRANSPORTE PRIVADO',
        'TRANSPORTE DE CARGA',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(GuideDetail::class);
    }
}
