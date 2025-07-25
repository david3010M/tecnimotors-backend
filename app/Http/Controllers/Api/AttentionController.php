<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\budgetSheet;
use App\Models\ConceptMov;
use App\Models\DetailAttention;
use App\Models\DocAlmacen;
use App\Models\Docalmacen_details;
use App\Models\Element;
use App\Models\ElementForAttention;
use App\Models\Product;
use App\Models\RouteImages;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionController extends Controller
{
    /**
     * Get all Attentions
     *
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/attention",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="query",
     *         description="ID of the vehicle to filter attentions",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="attention_status",
     *         in="query",
     *         description="Status of the attention to filter attentions",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of attentions",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Attention")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/attention?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/attention?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/attention"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Obtén el ID del vehículo y el estado de atención desde la solicitud
        $vehicleId       = $request->input('vehicle_id');
        $attentionStatus = $request->input('attention_status');

        // Consulta base para Attention con relaciones que incluyen eliminados
        $query = Attention::with([
            'worker.person'  => function ($query) {
                $query->withTrashed();
            },
            'vehicle'        => function ($query) {
                $query->withTrashed();
            },
            'vehicle.person' => function ($query) {
                $query->withTrashed();
            },
            'details'        => function ($query) {

            },
            'routeImages'    => function ($query) {

            },
            'elements'       => function ($query) {
                $query->withTrashed();
            },
            'concession'     => function ($query) {

            },
        ]);

        // Filtra por ID de vehículo si se proporciona
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Filtra por estado de atención si se proporciona
        if ($attentionStatus) {
            $query->where('status', $attentionStatus);
        }

        // Obtén la paginación con 15 registros por página (esto incluye el total)
        $objects = $query->orderBy('id', 'desc')->paginate(15);

        // Transforma cada elemento de la colección paginada
        $objects->getCollection()->transform(function ($attention) {
            $attention->elements = $attention->getElements($attention->id);
            $attention->details  = $attention->getDetails($attention->id);
            $attention->task     = $attention->getTask($attention->id);
            return $attention;
        });

        // Devuelve la colección transformada como respuesta JSON, incluyendo toda la información de la paginación
        return response()->json([
            'total'          => $objects->total(),           // Total de registros
            'data'           => $objects->items(),           // Los registros de la página actual
            'current_page'   => $objects->currentPage(),     // Página actual
            'last_page'      => $objects->lastPage(),        // Última página disponible
            'per_page'       => $objects->perPage(),         // Cantidad de registros por página
            'first_page_url' => $objects->url(1),            // URL de la primera página
            'from'           => $objects->firstItem(),       // Primer registro de la página actual
            'next_page_url'  => $objects->nextPageUrl(),     // URL de la siguiente página
            'path'           => $objects->path(),            // Ruta base de la paginación
            'prev_page_url'  => $objects->previousPageUrl(), // URL de la página anterior
            'to'             => $objects->lastItem(),        // Último registro de la página actual
        ]);
    }

    /**
     * Get a single Attention
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/attention/{id}",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Attention ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attention data",
     *         @OA\JsonContent(ref="#/components/schemas/Attention")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attention not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $object = Attention::find($id);
        if (! $object) {
            return response()->json(['message' => 'Attention not found'], 404);
        }

        $object           = Attention::with(['worker.person', 'vehicle', 'vehicle.person', 'details', 'routeImages', 'elements', 'concession'])->find($id);
        $object->elements = $object->getElements($object->id);
        $object->details  = $object->getDetails($object->id);
        $object->task     = $object->getTask($object->id);
        return response()->json($object);
    }

    /**
     * Get a single Attention by number
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/searchByNumber/{number}",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="number",
     *         in="path",
     *         required=true,
     *         description="Attention number in the format OTRS-00000001",
     *         @OA\Schema(type="string", example="OTRS-00000001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attention data",
     *         @OA\JsonContent(ref="#/components/schemas/Attention")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attention not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function searchByNumber($number)
    {

        $attention = Attention::where('number', $number)->first();

        if (! $attention) {
            return response()->json(['message' => 'Attention not found'], 404);
        }

        $object = Attention::with(['worker.person', 'vehicle', 'vehicle.person',
            'details', 'details.product.unit', 'routeImages', 'elements', 'concession'])
            ->where('number', $number)->first();
        $object->elements = $object->getElements($object->id);

        $object->technicians = $object->technicians($object->id);

        return response()->json($object);
    }

    /**
     * Create a new ATTENTION
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/attention",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Attention data",
     *         @OA\JsonContent(
     *             @OA\Property(property="arrivalDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *             @OA\Property(property="deliveryDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *             @OA\Property(property="observations", type="string", example="-"),
     *        @OA\Property(property="correlativo", type="string", example="123456"),
     *             @OA\Property(property="fuelLevel", type="string", example="10"),
     *             @OA\Property(property="km", type="string", example="0.00"),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="worker_id", type="integer", example=1),
     *             @OA\Property(property="concession_id", type="integer", example=1),
     *             @OA\Property(property="typeMaintenance", type="string", example="Preventivo"),
     *      @OA\Property(property="driver", type="string", example="Driver"),
     *             @OA\Property(
     *                 property="details",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="service_id", type="integer", example=1),
     *                     @OA\Property(property="worker_id", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="elements",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     * @OA\Property(
     *     property="detailsProducts",
     *     type="array",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(
     *             property="idProduct",
     *             type="integer",
     *             example=1
     *         ),
     *         @OA\Property(
     *             property="quantity",
     *             type="integer",
     *             example=2
     *         )
     *     )
     * )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element created",
     *         @OA\JsonContent(ref="#/components/schemas/Attention")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [

            'correlativo'                 => 'required|numeric|unique:attentions,correlativo,NULL,id,deleted_at,NULL',

            'arrivalDate'                 => 'required',
            'deliveryDate'                => 'required',
            'observations'                => 'nullable',
            'fuelLevel'                   => 'required|in:0,2,4,6,8,10',
            'km'                          => 'required',
            'routeImage.*'                => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'vehicle_id'                  => 'required|exists:vehicles,id',
            'worker_id'                   => 'required|exists:workers,id',
            'concession_id'               => 'nullable|exists:concessions,id',
            'driver'                      => 'nullable|string',

            'typeMaintenance'             => 'required|in:' . Attention::MAINTENICE_CORRECTIVE . ',' . Attention::MAINTENICE_PREVENTIVE,

            'elements'                    => 'nullable',
            'elements.*'                  => 'exists:elements,id',
            'details'                     => 'nullable',
            'detailsProducts'             => 'nullable',
            'detailsProducts.*.idProduct' => 'required|exists:products,id',
        ]);

        if (! $request->input('details')) {
            return response()->json(['error' => 'Atención sin Servicios'], 409);
        }


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $tipo         = 'OTRS';
        $resultado    = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM attentions a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

        $data = [
            'number'          => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'correlativo'     => $request->input('correlativo') ?? null,
            'arrivalDate'     => $request->input('arrivalDate') ?? null,
            'deliveryDate'    => $request->input('deliveryDate') ?? null,
            'observations'    => $request->input('observations') ?? null,
            'typeofDocument'  => $request->input('typeofDocument') ?? null,
            'fuelLevel'       => $request->input('fuelLevel') ?? null,
            'km'              => $request->input('km') ?? null,
            'routeImage'      => 'ruta.jpg',
            'vehicle_id'      => $request->input('vehicle_id') ?? null,
            'driver'          => $request->input('driver') ?? null,
            'concession_id'   => $request->input('concession_id') ?? null,
            'typeMaintenance' => $request->input('typeMaintenance') ?? null,

            'worker_id'       => $request->input('worker_id') ?? null,
        ];

        $object = Attention::create($data);

        $totalQuantityProducts = 0;

        if ($object) {
            //ASIGNAR ELEMENTS
            $elements = $request->input('elements') ?? [];
            foreach ($elements as $element) {
                $objectData = [
                    'element_id'   => $element,
                    'attention_id' => $object->id,
                ];
                ElementForAttention::create($objectData);
            }

            //ASIGNAR PRODUCTS
            $detailsProducts = $request->input('detailsProducts') ?? [];

            $sumProducts = 0;

            // Verificar si $detailsProducts tiene registros
            if ($detailsProducts != []) {
                foreach ($detailsProducts as $productDetail) {
                    $idProduct = $productDetail['idProduct'];
                    $quantity  = $productDetail['quantity'] ?? 1;

                    $product = Product::find($idProduct);

                    $objectData = [
                        'saleprice'    => $product->sale_price ?? '0.00',
                        'type'         => 'Product',
                        'quantity'     => $quantity,
                        'comment'      => '-',
                        'status'       => 'Generada',
                        'dateRegister' => Carbon::now(),
                        'dateMax'      => null,
                        'worker_id'    => null,
                        'product_id'   => $product->id ?? null,
                        'service_id'   => null,
                        'attention_id' => $object->id,
                    ];
                    $detailProd = DetailAttention::create($objectData);
                    $sumProducts += $detailProd->saleprice * $quantity;
                    $totalQuantityProducts += $quantity;
                }

                $object->totalProducts = $sumProducts;
            }

            //ASIGNAR DETAILS
            $detailsAttentions = $request->input('details') ?? [];
            $sumServices       = 0;
            foreach ($detailsAttentions as $detail) {

                $service    = Service::find($detail['service_id']);
                $objectData = [
                    'saleprice'    => $service->saleprice ?? '0.00',
                    'type'         => 'Service',
                    'comment'      => $detail['comment'] ?? '-',
                    'status'       => $detail['status'] ?? 'Generada',
                    'dateRegister' => Carbon::now(),
                    'dateMax'      => $request->input('deliveryDate') ?? null,

                    'worker_id'    => $detail['worker_id'],
                    'product_id'   => $detail['product_id'] ?? null,
                    'service_id'   => $detail['service_id'],
                    'attention_id' => $object->id,
                    'period'       => isset($detail['period']) ? $detail['period'] : 0,
                ];
                $detailService = DetailAttention::create($objectData);
                $sumServices += $detailService->saleprice;
            }

            $object->totalService = $sumServices;

        }
        logger($object->totalService);
        logger($object->totalProducts);
        $object->total = $object->totalService + $object->totalProducts;
        logger($object->total);
        $object->save();

        //IMAGEN
        $images = $request->file('routeImage') ?? [];
        $index  = 1;
        foreach ($images as $image) {

            $file        = $image;
            $currentTime = now();
            $filename    = $index . '-' . $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();

            $originalName = str_replace(' ', '_', $file->getClientOriginalName());
            $filename     = $index . '-' . $currentTime->format('YmdHis') . '_' . $originalName;
            $path         = $file->storeAs('public/photosSheetService', $filename);
            $routeImage   = 'https://develop.garzasoft.com/tecnimotors-backend/storage/app/' . $path;

            // $rutaImagen = Storage::url($path);
            $object->routeImage = $routeImage;
            $object->save();
            $index++;
            $dataImage = [
                'route'        => $routeImage,
                'attention_id' => $object->id,
            ];
            RouteImages::create($dataImage);
        }

        $object           = Attention::with(['worker.person', 'vehicle', 'details', 'routeImages', 'concession'])->find($object->id);
        $object->elements = $object->getElements($object->id);
        $object->details  = $object->getDetails($object->id);
        $object->task     = $object->getTask($object->id);

        if ($detailsProducts != []) {

            $conceptMov = ConceptMov::find(3);
            $docAlmacen = DocAlmacen::create([
                'sequentialnumber' => $this->nextCorrelative(DocAlmacen::class, 'sequentialnumber'),
                'date_moviment'    => $object->deliveryDate,
                'quantity'         => $totalQuantityProducts,
                'comment'          => 'Salida de Producto por Atención',
                'typemov'          => DocAlmacen::TIPOMOV_EGRESO,
                'concept'          => $conceptMov->name,
                'user_id'          => auth()->user()->id,
                'person_id'        => $object->vehicle->person->id,
                'concept_mov_id'   => $conceptMov->id,
                'attention_id'     => $object->id,
            ]);

            foreach ($detailsProducts as $productDetail) {
                $idProduct = $productDetail['idProduct'];
                $quantity  = $productDetail['quantity'] ?? 1;
                $product   = Product::find($idProduct);
                Docalmacen_details::create([
                    'sequentialnumber' => $this->nextCorrelative(Docalmacen_details::class, 'sequentialnumber'),
                    'quantity'         => $quantity,
                    'comment'          => 'Detalle de Salida de Producto por Atención',
                    'product_id'       => $product->id,
                    'doc_almacen_id'   => $docAlmacen->id,
                ]);
                $product->stock -= $quantity;
                $product->save();
            }
        }

        return response()->json($object);
    }

    /**
     * Update an attention
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/attention/{id}",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the attention",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","worker_id"},
     *      @OA\Property(property="arrivalDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *      @OA\Property(property="deliveryDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *      @OA\Property(property="observations", type="string", example="-"),
     *     @OA\Property(property="correlativo", type="string", example="123456"),
     *     @OA\Property(property="fuelLevel", type="string", example="2"),
     *     @OA\Property(property="km", type="string", example="0.00"),
     *     @OA\Property(property="driver", type="string", example="Driver"),
     *     @OA\Property(property="vehicle_id", type="string", example=1),
     *     @OA\Property(property="worker_id", type="string", example=1),
     *     @OA\Property(property="concession_id", type="integer", example=1),
     *     @OA\Property(property="typeMaintenance", type="string", example="Preventivo"),
     *     @OA\Property(
     *                 property="details",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                      @OA\Property(property="idDetail", type="string", example="1"),
     *                      @OA\Property(property="service_id", type="integer", example=1),
     *                       @OA\Property(property="worker_id", type="integer", example=1),
     *                  ),
     *     ),
     *    @OA\Property(
     *     property="detailsProducts",
     *     type="array",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(property="idDetail", type="string", example="1"),
     * @OA\Property(property="idProduct", type="integer", example=1),
     *         @OA\Property(
     *             property="quantity",
     *             type="integer",
     *             example=2
     *         )
     *      )
     *     ),
     *
     *          @OA\Property(
     *                 property="elements",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="attention updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Attention")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="attention not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $object = Attention::find($id);
        if (! $object) {
            return response()->json(['message' => 'attention not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'arrivalDate'                 => 'required',

            'correlativo'                 => "required|numeric|unique:attentions,correlativo,{$id},id,deleted_at,NULL",

            'deliveryDate'                => 'required',
            'observations'                => 'nullable',
            'fuelLevel'                   => 'required',
            'km'                          => 'required',

            'vehicle_id'                  => 'required|exists:vehicles,id',
            'worker_id'                   => 'required|exists:workers,id',
            'concession_id'               => 'nullable|exists:concessions,id',
            'driver'                      => 'nullable|string',

            'typeMaintenance'             => 'nullable|in:' . Attention::MAINTENICE_CORRECTIVE . ',' . Attention::MAINTENICE_PREVENTIVE,

            'elements'                    => 'nullable|array',
            'elements.*'                  => 'exists:elements,id',
            'details'                     => 'nullable',
            'detailsProducts'             => 'nullable',
            'detailsProducts.*.idProduct' => 'required|exists:products,id',
        ]);

        // $validator->after(function ($validator) use ($request) {
        //     foreach ($request->input('detailsProducts', []) as $index => $detail) {
        //         $productId = $detail['idProduct'] ?? null;
        //         $quantity = $detail['quantity'] ?? 0;

        //         if ($productId) {
        //             $product = Product::find($productId);
        //             if ($product && $product->stock < $quantity) {
        //                 $validator->errors()->add(
        //                     "detailsProducts.{$index}.quantity",
        //                     "The requested quantity for product {$product->name} exceeds the available stock ({$product->stock})."
        //                 );
        //             }
        //         }
        //     }
        // });

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'correlativo'     => $request->input('correlativo') ?? null,
            'arrivalDate'     => $request->input('arrivalDate') ?? null,
            'deliveryDate'    => $request->input('deliveryDate') ?? null,
            'observations'    => $request->input('observations') ?? null,
            'typeofDocument'  => $request->input('typeofDocument') ?? null,
            'fuelLevel'       => $request->input('fuelLevel') ?? null,
            'km'              => $request->input('km') ?? null,
            'routeImage'      => 'ruta.jpg',
            'vehicle_id'      => $request->input('vehicle_id') ?? null,
            'driver'          => $request->input('driver') ?? null,
            'concession_id'   => $request->input('concession_id') ?? null,
            'typeMaintenance' => $request->input('typeMaintenance') ?? null,

            'worker_id'       => $request->input('worker_id') ?? null,
        ];
        $totalQuantityProducts = 0;

        $object->update($data);

        $details = $request->input('elements', []);
        $object->setElements($object->id, $details);

        $detailsAt = $request->input('details', []);
        $object->setDetails($object->id, $detailsAt, $request->input('deliveryDate'));

        $detailsProducts = $request->input('detailsProducts') ?? [];

        // Verificar si $detailsProducts tiene registros
        if ($detailsProducts != []) {
            $object->setDetailProducts($object->id, $detailsProducts);
            $docAlmacen = DocAlmacen::where('attention_id', $object->id)->first();
            if (! isset($docAlmacen)) {
                if ($detailsProducts != []) {

                    $conceptMov = ConceptMov::find(1);
                    $docAlmacen = DocAlmacen::create([
                        'sequentialnumber' => $this->nextCorrelative(DocAlmacen::class, 'sequentialnumber'),
                        'date_moviment'    => $object->deliveryDate,
                        'quantity'         => $totalQuantityProducts,
                        'comment'          => 'Salida de Producto por Atención',
                        'typemov'          => DocAlmacen::TIPOMOV_EGRESO,
                        'concept'          => $conceptMov->name,
                        'user_id'          => auth()->user()->id,
                        'person_id'        => $object->vehicle->person->id,
                        'concept_mov_id'   => $conceptMov->id,
                        'attention_id'     => $object->id,
                    ]);

                    foreach ($detailsProducts as $productDetail) {
                        $idProduct = $productDetail['idProduct'];
                        $quantity  = $productDetail['quantity'] ?? 1;
                        $product   = Product::find($idProduct);
                        Docalmacen_details::create([
                            'sequentialnumber' => $this->nextCorrelative(Docalmacen_details::class, 'sequentialnumber'),
                            'quantity'         => $quantity,
                            'comment'          => 'Detalle de Salida de Producto por Atención',
                            'product_id'       => $product->id,
                            'doc_almacen_id'   => $docAlmacen->id,
                        ]);
                        $product->stock -= $quantity;
                        $product->save();
                    }
                }
            } else {
                foreach ($detailsProducts as $productDetail) {
                    $quantity = $productDetail['quantity'] ?? 1;
                    $totalQuantityProducts += $quantity;

                    if ($docAlmacen) {
                        $docAlmacen->quantity = $totalQuantityProducts;
                        $docAlmacen->save();
                    }
                }
            }

        }

        $object->total = $object->details()->get()->sum(function ($detail) {
            return $detail->saleprice * $detail->quantity;
        });
        $object->save();

        $images = $request->file('routeImage') ?? [];
        $object->setImages($object->id, $images);

        $object           = Attention::with(['worker.person', 'vehicle', 'details', 'routeImages', 'concession'])->find($object->id);
        $object->elements = $object->getElements($object->id);
        $object->details  = $object->getDetails($object->id);
        $object->task     = $object->getTask($object->id);

        $budgetSheet = budgetSheet::where('attention_id', $object->id)->first();
        if ($budgetSheet) {
            $budgetSheet->totalService  = $object->totalService;
            $budgetSheet->totalProducts = $object->totalProducts;
            $budgetSheet->subtotal      = floatval($object->total) / 1.18;
            $subtotal                   = floatval($object->total) / 1.18;
            $budgetSheet->igv           = $subtotal * 0.18;
            $budgetSheet->total         = $object->total;
            $budgetSheet->save();
        }
        return response()->json($object);
    }

    /**
     * Delete an Attention
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/attention/{id}",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Attention ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attention deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attention deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attention not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Attention has attentions for attention",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attention has attentions for attention")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $object = Attention::find($id);
        if (! $object) {
            return response()->json(['message' => 'Attention not found'], 404);
        }
        $detailsNotGenerated = $object->details()->where('type', 'Service')
            ->where('status', '!=', 'Generada')->exists();

        if ($detailsNotGenerated) {
            return response()->json(['message' => 'Exiten Servicios que ya estan siendo Procesados'], 409);
        }

        if ($object->documentoscarga()->exists()) {
            return response()->json(['message' => 'Existen documentos de almacén asociados'], 409);
        }
        
        
        $budgetSheet = $object->budgetSheet()->exists();
        if ($budgetSheet) {
            return response()->json(['message' => 'Orden de Servicio ya presupuestada'], 409);
        }

        $object->delete();

        return response()->json(['message' => 'Attention deleted']);
    }

    /**
     *  Retrieve the next correlativo value
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/getCorrelative",
     *     tags={"Attention"},
     *     summary="Get next correlativo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Next correlativo retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property( property="correlativo", type="string", example="123456" )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid request."
     *             )
     *         )
     *     )
     * )
     */
    public function getCorrelativo()
    {
        return response()->json([
            'correlativo' => Attention::getNextCorrelativo(),
        ]);
    }

}
