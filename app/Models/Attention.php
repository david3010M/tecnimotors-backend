<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Attention",
 *     type="object",
 *     @OA\Property(property="number", type="string", example="12345"),
 *     @OA\Property(property="arrivalDate", type="string", format="date", example="2024-05-21"),
 *     @OA\Property(property="deliveryDate", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="observations", type="string", example="Some observations here."),
 *     @OA\Property(property="fuelLevel", type="integer", example="80"),
 *     @OA\Property(property="km", type="integer", example="15000"),
 *     @OA\Property(property="routeImage", type="string", example="http://example.com/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25")
 * )
 */

class Attention extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'attentions';

    protected $fillable = [
        'number',
        'arrivalDate',
        'deliveryDate',
        'observations',
        'fuelLevel',
        'km',
        'routeImage',
        'worker_id',
        'vehicle_id',

        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function worker()
    {
        return $this->belongsTo(Person::class, 'worker_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(Person::class, 'vehicle_id');
    }

    public function elementForAttention()
    {
        return $this->hasMany(ElementForAttention::class);
    }

    public function getElements($id)
    {
        $list = ElementForAttention::where('attention_id', $id)->pluck('element_id')->toArray();
        return ($list);
    }

    public function setElements($id, $elementsId)
    {
        $attention = Attention::find($id);
        $currentOptionMenuIds = $attention->elementForAttention()->pluck('element_id')->toArray();

        $toAdd = array_diff($elementsId, $currentOptionMenuIds);
        $toRemove = array_diff($currentOptionMenuIds, $elementsId);

        if (!empty($toRemove)) {
            $attention->elementForAttention()->whereIn('element_id', $toRemove)->forceDelete();
        }

        // Añadir los nuevos accesos que no existen actualmente
        foreach ($toAdd as $optionMenuId) {

            if (!in_array($optionMenuId, $currentOptionMenuIds)) {
                ElementForAttention::create([
                    'attention_id' => $id,
                    'element_id' => $optionMenuId,
                ]);
            }
        }
    }

    //REVISAR QUE NO TENGA RELACIONES
}
