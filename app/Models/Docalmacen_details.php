<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @OA\Schema(
 *     schema="DocAlmacenDetails",
 *     title="Detalles del Documento de Almacén",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="sequentialnumber", type="string", example="DMA1-00000001"),
 *     @OA\Property(property="quantity", type="number", format="integer", example="15"),
 *     @OA\Property(property="comment", type="string", example="Pago de factura para el producto X"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-22 00:43:09")
 * )
 */

class Docalmacen_details extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'sequentialnumber',
        'quantity',
        'doc_almacen_id',
        'product_id',
        'comment',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'sequentialnumber',
        'comment' => 'like',
        'product.name' => 'like',
        'quantity' => 'like',
    ];

    const sorts = [
        'id',
        'product.name',
        'sequentialnumber',
        'comment',
    ];

    public function docalmacen()
    {
        return $this->belongsTo(DocAlmacen::class, 'doc_almacen_id')->with(['user', 'concept_mov']);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
