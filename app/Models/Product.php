<?php

namespace App\Models;

use App\Http\Resources\SaleProductResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="product name"),
 *     @OA\Property(property="purchase_price", type="number", example="100.00"),
 *     @OA\Property(property="percentage", type="number", example="10.00"),
 *     @OA\Property(property="sale_price", type="number", example="110.00"),
 *     @OA\Property(property="stock", type="number", example="100"),
 *     @OA\Property(property="quantity", type="number", example="10"),
 *     @OA\Property(property="type", type="string", example="product type"),
 *     @OA\Property(property="category_id", type="integer", example="2"),
 *     @OA\Property(property="unit_id", type="integer", example="1"),
 *     @OA\Property(property="brand_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="category", type="object", ref="#/components/schemas/Category"),
 *     @OA\Property(property="unit", type="object", ref="#/components/schemas/Unit"),
 *     @OA\Property(property="brand", type="object", ref="#/components/schemas/Brand"),
 * )
 *
 * @OA\Schema(
 *      schema="ProductNoRelations",
 *      title="Product",
 *      description="Product model",
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="name", type="string", example="product name"),
 *      @OA\Property(property="purchase_price", type="number", example="100.00"),
 *      @OA\Property(property="percentage", type="number", example="10.00"),
 *      @OA\Property(property="sale_price", type="number", example="110.00"),
 *      @OA\Property(property="stock", type="number", example="100"),
 *      @OA\Property(property="quantity", type="number", example="10"),
 *      @OA\Property(property="type", type="string", example="product type"),
 *      @OA\Property(property="category_id", type="integer", example="2"),
 *      @OA\Property(property="unit_id", type="integer", example="1"),
 *      @OA\Property(property="brand_id", type="integer", example="1"),
 *      @OA\Property(property="created_at", type="string", format="date-time")
 *  )
 *
 *
 * @OA\Schema(
 *     schema="ProductRequest",
 *     title="ProductRequest",
 *     description="Product request model",
 *     @OA\Property(property="name", type="string", example="product name"),
 *     @OA\Property(property="purchase_price", type="number", example="100.00"),
 *     @OA\Property(property="percentage", type="number", example="10.00"),
 *     @OA\Property(property="sale_price", type="number", example="110.00"),
 *     @OA\Property(property="stock", type="number", example="100"),
 *     @OA\Property(property="quantity", type="number", example="10"),
 *     @OA\Property(property="type", type="string", example="product type"),
 *     @OA\Property(property="category_id", type="integer", example="2"),
 *     @OA\Property(property="unit_id", type="integer", example="1"),
 *     @OA\Property(property="brand_id", type="integer", example="1"),
 * )
 *
 */
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'purchase_price',
        'percentage',
        'sale_price',
        'stock',
        'quantity',
        'type',
        'category_id',
        'unit_id',
        'brand_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function details()
    {
        return $this->hasMany(DetailAttention::class);
    }

    public static function getSaleProducts($plate = null, $productId = null, $from = null, $to = null)
    {
        $query = Product::whereHas('details', function ($query) use ($plate, $from, $to) {
            if ($plate) {
                $query->whereHas('attention', function ($query) use ($plate) {
                    $query->whereHas('vehicle', function ($query) use ($plate) {
                        $query->where('plate', $plate);
                    });
                });
            }

            if ($from && $to) {
                $query->whereBetween('dateRegister', [$from, $to]);
            } elseif ($from) {
                $query->where('dateRegister', '>=', $from);
            } elseif ($to) {
                $query->where('dateRegister', '<=', $to);
            }
        });

        if ($productId) {
            $query->where('id', $productId);
        }

        $products = $query->with(['details.attention.vehicle', 'details.attention.budgetSheet'])->get();

        return $products->flatMap(function ($product) use ($plate) {
            $filteredDetails = $product->details->filter(function ($detail) use ($plate) {
                if ($plate) {
                    return $detail->attention->vehicle->plate === $plate;
                }
                return true;
            });

            return $filteredDetails->map(function ($detail) use ($product) {
                $product->detail = $detail;
                return new SaleProductResource($product);
            });
        });
    }


}
