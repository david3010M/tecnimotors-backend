<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteReason extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['code', 'description', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
    protected $casts = ['created_at' => 'datetime'];

    const filters = ['code', 'description', 'created_at'];
    const sorts = ['id', 'code', 'description', 'created_at'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
