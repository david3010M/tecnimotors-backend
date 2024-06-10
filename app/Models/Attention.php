<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema (
 *     schema="Attention",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="12345"),
 *     @OA\Property(property="arrivalDate", type="string", format="date", example="2024-05-21"),
 *     @OA\Property(property="deliveryDate", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="observations", type="string", example="Some observations here."),
 *     @OA\Property(property="fuelLevel", type="integer", example="80"),
 *     @OA\Property(property="km", type="integer", example="15000"),
 *     @OA\Property(property="routeImage", type="string", example="/image.jpg"),
 *      @OA\Property(property="totalService", type="number", example="100.00"),
 *      @OA\Property(property="totalProducts", type="number", example="200.00"),
 *      @OA\Property(property="total", type="number", example="300.00"),
 *      @OA\Property(property="debtAmount", type="number", example="100.00"),
 *     @OA\Property(property="percentage", type="integer", example="1"),

 *
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25"),
 *         @OA\Property(
 *         property="worker",
 *         ref="#/components/schemas/Worker"
 *     ),@OA\Property(
 *         property="vehicle",
 *         ref="#/components/schemas/Vehicle"
 *     ),
 * @OA\Property(
 *     property="RouteImages",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/RouteImages")
 * ),

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
        'percentage',
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

    public function technicians($id)
    {
        $attention = Attention::find($id);
        if (!$attention) {
            return response()->json(['message' => 'Attention not found'], 404);
        }
        $technicians = $attention->details()
            ->where('type', 'Service')
            ->with(['worker.person'])
            ->get()
            ->pluck('worker.person')->unique('id');
        return $technicians;
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

    public function details()
    {
        return $this->hasMany(DetailAttention::class)->orderBy('type', 'desc')->with(['worker', 'service', 'product']);
    }
    public function routeImages()
    {
        return $this->hasMany(RouteImages::class);
    }

    public function elements()
    {
        return $this->hasMany(ElementForAttention::class);
    }

    public static function getAttention($id)
    {
        $object = Attention::with([
            'worker.person',
            'vehicle.person',
            'vehicle.brand',
            'details',
            'elements.element',
        ])->find($id);

        if (!$object) {
            abort(404, 'Attention not found');
        }

        return $object;
    }

    public function setElements($id, $elementsId)
    {
        $attention = Attention::find($id);
        $currentOptionMenuIds = $attention->elementForAttention()->pluck('element_id')->toArray();

        $toAdd = array_diff($elementsId, $currentOptionMenuIds);
        $toRemove = array_diff($currentOptionMenuIds, $elementsId);

        foreach ($toAdd as $optionMenuId) {

            if (!in_array($optionMenuId, $currentOptionMenuIds)) {
                ElementForAttention::create([
                    'attention_id' => $id,
                    'element_id' => $optionMenuId,
                ]);
            }
        }
        if (!empty($toRemove)) {
            $attention->elementForAttention()->whereIn('element_id', $toRemove)->delete();
        }
    }

    public function setDetails($id, $detailServices)
    {
        $attention = Attention::find($id);
        $currentDetailsIds = $attention->details()->where('type', 'Service')->pluck('id')->toArray();
        $detailsUpdate = $detailServices;

        $newDetailIds = [];

        foreach ($detailsUpdate as $detailData) {
            $idDetail = isset($detailData['idDetail']) == false ? 'null' : $detailData['idDetail'];

            if (($idDetail != 'null')) {
                $newDetailIds[] = $idDetail;
                $detail = DetailAttention::find($idDetail);
                if ($detail) {
                    $data = [
                        'service_id' => $detailData['service_id'],
                        'worker_id' => $detailData['worker_id'],
                    ];
                    $detail->update($data);
                }
            } else {
                $detail = $detailData;
                $service = Service::find($detail['service_id']);
                $objectData = [
                    'saleprice' => $service->saleprice ?? '0.00',
                    'type' => 'Service',
                    'comment' => $detail['comment'] ?? '-',
                    'status' => $detail['status'] ?? 'Generada',
                    'dateRegister' => Carbon::now(),
                    'dateMax' => $detail['dateMax'] ?? null,
                    'worker_id' => $detail['worker_id'],
                    'product_id' => $detail['product_id'] ?? null,
                    'service_id' => $detail['service_id'],
                    'attention_id' => $attention->id,
                ];
                $detailService = DetailAttention::create($objectData);
                $newDetailIds[] = $detailService->id;
            }
        }

        $detailsToDelete = array_diff($currentDetailsIds, $newDetailIds);
        $attention->details()->where('type', 'Service')->whereIn('id', $detailsToDelete)->delete();

        $attention->totalService = $attention->details()->where('type', 'Service')->sum('saleprice');
        $attention->save();
    }

    public function setDetailProducts($id, $detailsAtId)
    {
        $attention = Attention::find($id);
        $currentDetailsIds = $attention->details()->where('type', 'Product')->pluck('id')->toArray();
        $detailsUpdate = $detailsAtId;

        $newDetailIds = [];

        foreach ($detailsUpdate as $detailData) {
            $idDetail = isset($detailData['idDetail']) == false ? 'null' : $detailData['idDetail'];

            if (($idDetail != 'null')) {
                $newDetailIds[] = $idDetail;

                $detail = DetailAttention::find($idDetail);

                if ($detail) {
                    $data = [
                        'quantity' => $detailData['quantity'],
                        'product_id' => $detailData['idProduct'],
                    ];
                    $detail->update($data);

                }
            } else {
                $detail = $detailData;
                $idProduct = $detail['idProduct'];
                $quantity = $detail['quantity'];

                $product = Product::find($idProduct);
                $objectData = [
                    'saleprice' => $product->sale_price ?? '0.00',
                    'type' => 'Product',
                    'quantity' => $quantity,
                    'comment' => '-',
                    'status' => 'Generada',
                    'dateRegister' => Carbon::now(),
                    'dateMax' => null,
                    'worker_id' => null,
                    'product_id' => $product->id ?? null,
                    'service_id' => null,
                    'attention_id' => $attention->id,
                ];
                $detailProd = DetailAttention::create($objectData);
                $newDetailIds[] = $detailProd->id;
            }
        }

        $detailsToDelete = array_diff($currentDetailsIds, $newDetailIds);
        $attention->details()->where('type', 'Product')->whereIn('id', $detailsToDelete)->delete();
        $sumaPrecios = $attention->details()->where('type', 'Product')->sum('saleprice');
        $sumaCantidades = $attention->details()->where('type', 'Product')->sum('quantity');
        $attention->totalProducts = $sumaPrecios * $sumaCantidades;
        $attention->save();
    }

    public function setImages($id, $images)
    {
        $attention = Attention::find($id);

        foreach ($attention->routeImages() as $routeImage) {
            $RouteImage = RouteImages::find($routeImage->id);
            $path = $RouteImage->route;
            if (Storage::exists($path)) {
                Storage::delete($path);

            }
        }

        $attention->routeImages()->delete();
        $index = 1;
        foreach ($images as $image) {

            $file = $image;
            $currentTime = now();
            $filename = $index . '-' . $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/photosSheetService', $filename);
            $rutaImagen = Storage::url($path);
            $attention->routeImage = $rutaImagen;
            $attention->save();

            $dataImage = [
                'route' => $rutaImagen,
                'attention_id' => $attention->id,
            ];

            RouteImages::create($dataImage);
            $index++;
        }

    }

}
