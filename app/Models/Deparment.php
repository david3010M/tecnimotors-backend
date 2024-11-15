<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deparment extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'ubigeo_code'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}
