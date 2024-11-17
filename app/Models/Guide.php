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
        'cod_motive',
        'modality',
        'recipient_names',
        'recipient_document',
        'driver_fullnames',
        'driver_names',
        'driver_surnames',
        'driver_document',
        'driver_licencia',
        'vehicle_placa',
        'nro_paquetes',
        'transbordo',
        'net_weight',
        'ubigeo_start',
        'address_start',
        'ubigeo_end',
        'address_end',
        'observation',
        'factura',
        'status_facturado',
        'user_id',
        'branch_id',
        'worker_id',
        'district_id_start',
        'district_id_end',
        'guide_motive_id',
        'recipient_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'transbordo' => 'boolean',
    ];

    const filters = [
        'number' => 'like',
        'date_emision' => '',
        'recipient_names' => 'like',
        'driver_fullnames' => 'like',
        'worker_id' => '=',
        'districtStart.name' => 'like',
        'districtEnd.name' => 'like',
        'observation' => 'like',
    ];
    const sorts = [];

    const motives = [
        'VENTA',
        'COMPRA',
    ];

    const STATUS_FACTURADO = 'FACTURADO';
    const STATUS_FACTURADO_PENDIENTE = 'PENDIENTE';

    const modalities = [
        'TRANSPORTE PUBLICO',
        'TRANSPORTE PRIVADO',
        'TRANSPORTE DE CARGA',
    ];

    const STATUS_PENDING = 'PENDIENTE';
    const STATUS_SENT = 'ENVIADO';

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

    public function recipient()
    {
        return $this->belongsTo(Person::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function districtStart()
    {
        return $this->belongsTo(District::class, 'district_id_start');
    }

    public function districtEnd()
    {
        return $this->belongsTo(District::class, 'district_id_end');
    }
}
