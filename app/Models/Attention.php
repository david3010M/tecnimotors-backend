<?php

namespace App\Models;

use Carbon\Carbon;
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
 *     @OA\Property(property="routeImage", type="string", example="/image.jpg"),
 * @OA\Property(property="totalService", type="number", example="100.00"),
 * @OA\Property(property="totalProducts", type="number", example="200.00"),
 * @OA\Property(property="total", type="number", example="300.00"),
 * @OA\Property(property="debtAmount", type="number", example="100.00"),
 *
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25"),
 *         @OA\Property(
 *         property="worker",
 *         ref="#/components/schemas/Worker"
 *     ),@OA\Property(
 *         property="vehicle",
 *         ref="#/components/schemas/Vehicle"
 *     ),
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
        'totalService',
        'totalProducts',
        'total',
        'debtAmount',

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
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
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

    public function getDetails($id)
    {
        $list = DetailAttention::where('attention_id', $id)->with(['worker', 'service', 'product'])->get();
        return $list;
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

    public function setDetails($id, $detailsAtId)
    {
        $attention = Attention::find($id);
        $currentDetailsAtds = $attention->details()->pluck('id')->toArray();

        $toAdd = array_diff($detailsAtId, $currentDetailsAtds);
        $toRemove = array_diff($currentDetailsAtds, $detailsAtId);

        if (!empty($toRemove)) {
            $attention->details()->whereIn('id', $toRemove)->forceDelete();
        }

        // Añadir los nuevos accesos que no existen actualmente
        foreach ($toAdd as $detailNew) {

            if (!in_array($detailNew, $currentDetailsAtds)) {
                $service = Service::find($detailNew['service_id']);
                $objectData = [
                    'saleprice' => $service->saleprice ?? '0.00',
                    'type' => 'Service',
                    'comment' => $detailNew['comment'] ?? '-',
                    'status' => $detailNew['status'] ?? 'Generada',
                    'dateRegister' => Carbon::now(),
                    'dateMax' => $detailNew['dateMax'] ?? null,
                    'worker_id' => $detailNew['worker_id'],
                    'product_id' => $detailNew['product_id'] ?? null,
                    'service_id' => $detailNew['service_id'],
                    'attention_id' => $attention->id,
                ];
                DetailAttention::create($objectData);
            }
        }
    }

    //REVISAR QUE NO TENGA RELACIONES

    public function details()
    {
        return $this->hasMany(DetailAttention::class)->orderBy('type', 'desc')->with(['worker', 'service', 'product']);
    }

    public function elements()
    {
//        return $this->belongsToMany(Element::class, 'element_for_attentions');
        return $this->hasMany(ElementForAttention::class);
    }


//    PDF

    public static function getAttention($id)
    {
        $object = Attention::with([
            'worker.person',
            'vehicle.person',
            'vehicle.brand',
            'details',
            'elements.element'
        ])->find($id);

        if (!$object) {
            abort(404, 'Attention not found');
        }

        return $object;
    }
}
