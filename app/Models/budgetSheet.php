<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="BudgetSheet",
 *     title="BudgetSheet",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="number", type="string", example="PRES-00000001"),
 *     @OA\Property(property="paymentType", type="string", example="Al Contado"),
 *     @OA\Property(property="totalService", type="number", format="float", example=0.00),
 *     @OA\Property(property="totalProducts", type="number", format="float", example=0.00),
 *     @OA\Property(property="debtAmount", type="number", format="float", example=0.00),
 *     @OA\Property(property="total", type="number", format="float", example=0.00),
 *     @OA\Property(property="discount", type="number", format="float", example=0.00),
 *     @OA\Property(property="subtotal", type="number", format="float", example=0.00),
 *     @OA\Property(property="igv", type="number", format="float", example=0.00),
 *     @OA\Property(
 *         property="Attention",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Attention")
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="BudgetSheetSingle",
 *      title="BudgetSheet",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="number", type="string", example="PRES-00000001"),
 *      @OA\Property(property="paymentType", type="string", example="Al Contado"),
 *      @OA\Property(property="totalService", type="number", format="float", example=0.00),
 *      @OA\Property(property="totalProducts", type="number", format="float", example=0.00),
 *      @OA\Property(property="debtAmount", type="number", format="float", example=0.00),
 *      @OA\Property(property="total", type="number", format="float", example=0.00),
 *      @OA\Property(property="discount", type="number", format="float", example=0.00),
 *      @OA\Property(property="subtotal", type="number", format="float", example=0.00),
 *      @OA\Property(property="igv", type="number", format="float", example=0.00),
 *  )
 */
class budgetSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'paymentType',
        'totalService',
        'totalProducts',
        'debtAmount',
        'total',
        'discount',
        'subtotal',
        'igv',
        'status',
        'attention_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function attention()
    {
        return $this->belongsTo(Attention::class, 'attention_id');
    }

    public static function getBudgetSheet($id)
    {
        // Consulta para recuperar el presupuesto y relaciones, incluyendo registros eliminados
        $object = budgetSheet::with([
            'attention' => function ($query) {
                $query->withTrashed(); // Incluye atención eliminada
            },
            'attention.worker.person' => function ($query) {
                $query->withTrashed(); // Incluye personas eliminadas
            },
            'attention.vehicle.person' => function ($query) {
                $query->withTrashed(); // Incluye personas eliminadas
            },
            'attention.vehicle.vehicleModel.brand' => function ($query) {
                $query->withTrashed(); // Incluye marcas eliminadas
            },
            'attention.details' => function ($query) {
                $query->withTrashed(); // Incluye detalles eliminados
            },
            'attention.details.product.unit' => function ($query) {
                $query->withTrashed(); // Incluye unidades de productos eliminadas
            },
            'attention.routeImages',
            'attention.elements' => function ($query) {
                $query->withTrashed(); // Incluye elementos eliminados
            },
        ])->find($id);

        if (!$object) {
            abort(404, 'BudgetSheet not found');
        }

        return $object;
    }


//    commitment
    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

}
