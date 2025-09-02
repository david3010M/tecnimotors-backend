<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportAttendanceVehicleResource;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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
 *     @OA\Property(property="totalService", type="number", example="100.00"),
 *     @OA\Property(property="totalProducts", type="number", example="200.00"),
 *     @OA\Property(property="total", type="number", example="300.00"),
 *     @OA\Property(property="debtAmount", type="number", example="100.00"),
 *     @OA\Property(property="percentage", type="integer", example="1"),
 *     @OA\Property(property="driver", type="string", example="Driver"),
 *     @OA\Property(property="concession_id", type="integer", example="1"),
 *     @OA\Property(property="typeMaintenance", type="string", example="Preventivo"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25"),
 *     @OA\Property(property="worker", ref="#/components/schemas/Worker"),
 *     @OA\Property(property="vehicle", ref="#/components/schemas/Vehicle"),
 *     @OA\Property(property="RouteImages", type="array", @OA\Items(ref="#/components/schemas/RouteImages")),
 * )
 *
 * @OA\Schema (
 *     schema="AttentionByPerson",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="12345"),
 *     @OA\Property(property="arrivalDate", type="string", format="date", example="2024-05-21"),
 *     @OA\Property(property="deliveryDate", type="string", format="date", example="2024-05-22"),
 *     @OA\Property(property="observations", type="string", example="Some observations here."),
 *     @OA\Property(property="fuelLevel", type="integer", example="80"),
 *     @OA\Property(property="km", type="integer", example="15000"),
 *     @OA\Property(property="totalService", type="number", example="100.00"),
 *     @OA\Property(property="totalProducts", type="number", example="200.00"),
 *     @OA\Property(property="total", type="number", example="300.00"),
 *     @OA\Property(property="debtAmount", type="number", example="100.00"),
 *     @OA\Property(property="percentage", type="integer", example="1"),
 *     @OA\Property(property="driver", type="string", example="Driver"),
 *     @OA\Property(property="routeImage", type="string", example="/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-21 04:09:25"),
 *     @OA\Property(property="worker_id", type="integer", example="1"),
 *     @OA\Property(property="vehicle_id", type="integer", example="1"),
 *     @OA\Property(property="budget_sheet", ref="#/components/schemas/BudgetSheetSingle"),
 *  )
 *
 */
class Attention extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'attentions';

    protected $fillable = [
        'number',
        'correlativo',

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

        'typeMaintenance',

        'worker_id',
        'vehicle_id',
        'concession_id',
        'driver',
        'status',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    const MAINTENICE_PREVENTIVE = 'Preventivo';
    const MAINTENICE_CORRECTIVE = 'Correctivo';

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function budgetSheet()
    {
        return $this->hasOne(budgetSheet::class);
    }

    public function concession()
    {
        return $this->belongsTo(Concession::class, 'concession_id')->with('client', 'concessionaire', 'routeImage');
    }

    public static function updateStatus($attentionId)
    {
        $attention = Attention::find($attentionId);
        $totalDetails = DetailAttention::where('attention_id', $attentionId)->count();
        $totalDetailsListos = DetailAttention::where('attention_id', $attentionId)->where('status', 'Listo')->count();

        if ($totalDetails == $totalDetailsListos) {
            $attention->status = 'Finalizada';
        } else {
            $attention->status = 'Pendiente';
        }

        $attention->save();
    }

    public static function getAttentionByMonths($from = null, $to = null)
    {
        $query = Attention::with([
            'vehicle.vehicleModel.brand',
            'vehicle.person',
            'details.service',
            'worker.person',
            'budgetSheet',
        ]);

        if ($from && $to) {
            $query->whereBetween('arrivalDate', [$from, $to]);
        } elseif ($from) {
            $query->where('arrivalDate', '>=', $from);
        } elseif ($to) {
            $query->where('arrivalDate', '<=', $to);
        }

        $attentions = $query->orderBy('arrivalDate')->get();
        $attentions = ReportAttendanceVehicleResource::collection($attentions);

        $attentionsMonths = $attentions->groupBy(function ($attention) {
            $meses = Constants::ES_MONTHS;
            $mes = Carbon::parse($attention->arrivalDate)->format('F');
            return $meses[$mes];
        });

        return $attentionsMonths;
    }

    public function technicians($id)
    {
        $attention = Attention::find($id);
        if (!$attention) {
            return response()->json(['message' => 'Attention not found'], 404);
        }
        $technicians = $attention->details()
            ->where('type', 'Service')
            ->with([
                'worker' => function ($query) {
                    $query->withTrashed(); // Incluye eliminados
                },
                'worker.person' => function ($query) {
                    $query->withTrashed(); // Incluye eliminados
                },
            ])
            ->get()
            ->pluck('worker.person')->unique('id');
        return $technicians;
    }

    // public function driver()
    // {
    //     return $this->belongsTo(Person::class, 'driver_id');
    // }

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

    public function getTask($id)
    {
        $list = Task::
            // with(['detailAttentions.attention'])->
            whereHas('detailAttentions.attention', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->get();
        return $list;
    }

    public function details()
    {
        return $this->hasMany(DetailAttention::class)->
            orderBy('type', 'desc')
            ->with(['worker', 'attention', 'attention.vehicle', 'attention.vehicle.person', 'service', 'product']);
    }

    public function routeImages()
    {
        return $this->hasMany(RouteImages::class);
    }

    public function routeImagesTask()
    {
        return $this->hasMany(RouteImages::class)->whereNotNull('task_id');
    }

    public function routeImagesAttention()
    {
        return $this->hasMany(RouteImages::class)->whereNull('task_id');
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
            'vehicle.vehicleModel.brand',
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

    public function getDetailsProducts()
    {
        return $this->details()->where('type', 'Product')->get()
            ->pluck('product');
    }

    public function setDetails(int $id, array $detailServices, $deliveryDate): void
{
    $attention = Attention::findOrFail($id);

    $currentIds = $attention->details()
        ->where('type', 'Service')
        ->pluck('id')->all();

    $newIds = [];

    foreach ($detailServices as $row) {
        $detailId  = $row['idDetail']   ?? null;
        $serviceId = $row['service_id'] ?? null;
        $workerId  = $row['worker_id']  ?? null;

        if ($detailId) {
            $detail = DetailAttention::where('id', $detailId)
                ->where('attention_id', $attention->id)
                ->first();

            if ($detail) {
                $detail->update([
                    'service_id' => $serviceId,
                    'worker_id'  => $workerId,
                    'period'     => $row['period']  ?? 0,
                    'dateMax'    => $deliveryDate,
                    'product_id' => null,
                    'comment'    => $row['comment'] ?? $detail->comment,
                    'status'     => $row['status']  ?? $detail->status,
                    // quantity fijo en 1 para servicios (evita nulls)
                    'quantity'   => $detail->quantity ?: 1,
                ]);
                $newIds[] = $detail->id;
            }
        } else {
            $service = Service::find($serviceId);
            $created = DetailAttention::create([
                'saleprice'    => $service->saleprice ?? 0.00,
                'type'         => 'Service',
                'comment'      => $row['comment'] ?? '-',
                'status'       => $row['status']  ?? 'Generada',
                'dateRegister' => Carbon::now(),
                'dateMax'      => $deliveryDate,
                'worker_id'    => $workerId,
                'product_id'   => null,
                'service_id'   => $serviceId,
                'period'       => $row['period'] ?? 0,
                'quantity'     => 1,
                'attention_id' => $attention->id,
            ]);
            $newIds[] = $created->id;
        }
    }

    $toDelete = array_diff($currentIds, $newIds);
    if (!empty($toDelete)) {
        $attention->details()
            ->where('type', 'Service')
            ->whereIn('id', $toDelete)
            ->delete();
    }

    $attention->totalService = (float) $attention->details()
        ->where('type', 'Service')
        ->sum('saleprice');

    $attention->save();
}


    public function setDetailProducts(int $id, array $details): void
{
    $attention = Attention::findOrFail($id);
    // $docAlmacen = DocAlmacen::where('attention_id', $attention->id)->first();

    $currentIds = $attention->details()
        ->where('type', 'Product')
        ->pluck('id')->all();

    $newIds = [];

    foreach ($details as $row) {
        $detailId  = $row['idDetail']  ?? null;
        $productId = $row['idProduct'] ?? null;
        $quantity  = (float) ($row['quantity'] ?? 0);

        if ($detailId) {
            $detail = DetailAttention::where('id', $detailId)
                ->where('attention_id', $attention->id)
                ->first();

            if ($detail) {
                $product = Product::find($productId);
                $detail->update([
                    'quantity'   => $quantity,
                    'product_id' => $productId,
                    'service_id' => null,
                    'saleprice'  => $product->sale_price ?? $detail->saleprice,
                ]);

                // Aquí iría tu lógica de DocAlmacen si corresponde
                $newIds[] = $detail->id;
            }
        } else {
            $product = Product::find($productId);
            $created = DetailAttention::create([
                'saleprice'    => $product->sale_price ?? 0.00,
                'type'         => 'Product',
                'quantity'     => $quantity,
                'comment'      => $row['comment'] ?? '-',
                'status'       => $row['status']  ?? 'Generada',
                'dateRegister' => Carbon::now(),
                'dateMax'      => null,
                'worker_id'    => null,
                'product_id'   => $product->id ?? null,
                'service_id'   => null,
                'attention_id' => $attention->id,
            ]);

            // Si usas DocAlmacen, descomenta e integra aquí
            $newIds[] = $created->id;
        }
    }

    $toDelete = array_diff($currentIds, $newIds);
    if (!empty($toDelete)) {
        $attention->details()
            ->where('type', 'Product')
            ->whereIn('id', $toDelete)
            ->delete();
    }

    $attention->totalProducts = (float) $attention->details()
        ->where('type', 'Product')
        ->selectRaw('COALESCE(SUM(saleprice * quantity),0) as total')
        ->value('total');

    $attention->save();
}


    public function setImages($id, $images)
    {
        $attention = Attention::find($id);

        $index = 1;

        if ($images != []) {
            foreach ($attention->routeImagesAttention as $routeImage) {

                $RouteImage = RouteImages::find($routeImage->id);
                $path = $RouteImage->route;
                $filePath = $path;

                $filePath = preg_replace('/^.*public\//', '', $filePath);

                // Si deseas eliminar la ruta de almacenamiento
                if ($filePath && Storage::disk('public')->exists($filePath)) {

                    Storage::disk('public')->delete($filePath);

                }
                $RouteImage->delete();

            }
        }

        foreach ($images as $image) {

            $file = $image;
            $currentTime = now();
            $filename = $index . '-' . $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();

            $originalName = str_replace(' ', '_', $file->getClientOriginalName());
            $filename = $index . '-' . $currentTime->format('YmdHis') . '_' . $originalName;
            $path = $file->storeAs('public/photosSheetService', $filename);
            $routeImage = 'https://develop.garzasoft.com/tecnimotors-backend/storage/app/' . $path;

            // $rutaImagen = Storage::url($path);
            $attention->routeImage = $routeImage;
            $attention->save();

            $dataImage = [
                'route' => $routeImage,
                'attention_id' => $attention->id,
            ];

            RouteImages::create($dataImage);
            $index++;
        }

    }
    public static function getNextCorrelativo(): int
    {
        // Obtener todos los registros ordenados por correlativo
        $correlativos = Attention::orderBy('correlativo', 'asc')
            ->pluck('correlativo')
            ->toArray();

        // Buscar el primer número faltante en la secuencia
        $siguienteNum = 1;
        foreach ($correlativos as $num) {
            if ($num == $siguienteNum) {
                $siguienteNum++;
            } else {
                break; // Encontramos el primer hueco
            }
        }

        return $siguienteNum;
    }
    public function documentoscarga()
    {
        return $this->hasMany(DocAlmacen::class);
    }

}
