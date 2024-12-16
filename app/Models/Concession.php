<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'concession',
        'registerDate',
        'client_id',
        'concessionaire_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'registerDate' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    const filters = [
        'concession' => 'like',
        'registerDate' => 'between',
        'client_id' => '=',
        'concessionaire_id' => '=',
    ];

    const sorts = [
        'concession',
        'registerDate',
        'client_id',
        'concessionaire_id',
    ];


    public function client()
    {
        return $this->belongsTo(Person::class, 'client_id');
    }

    public function concessionaire()
    {
        return $this->belongsTo(Person::class, 'concessionaire_id');
    }

    public function attentions()
    {
        return $this->hasMany(Attention::class);
    }

    public function routeImage()
    {
        return $this->hasOne(RouteImages::class);
    }

}
