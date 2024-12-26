<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="DocAlmacen",
 *     title="Documentos de Almacén",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="date_moviment", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="quantity", type="number", format="float", example="1500.75"),
 *     @OA\Property(property="comment", type="string", example="Pago de factura para el producto X"),
 *     @OA\Property(property="user_id", type="integer", example="4"),
 *     @OA\Property(property="concept_mov_id", type="integer", example="2"),
 *     @OA\Property(property="product_id", type="integer", example="5"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="concept_mov", ref="#/components/schemas/ConceptMov"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22 00:43:09")
 * )
 */



class DocAlmacen extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'date_moviment',
        'quantity',
        'comment',
        'user_id',
        'concept_mov_id',
        'product_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function concept_mov()
    {
        return $this->belongsTo(ConceptMov::class, 'concept_mov_id');
    }
}
