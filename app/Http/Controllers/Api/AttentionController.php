<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\DetailAttention;
use App\Models\Element;
use App\Models\ElementForAttention;
use App\Models\Product;
use App\Models\RouteImages;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttentionController extends Controller
{
    /**
     * Get all Attentions
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/attention",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Attentions",
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
    public function index()
    {
        // Obtenemos la paginación simple de 15 registros con las relaciones necesarias
        $objects = Attention::with(['worker.person', 'vehicle', 'driver', 'vehicle.person', 'details', 'routeImages', 'driver', 'elements'])->simplePaginate(15);

        // Transformamos cada elemento de la colección paginada
        $objects->getCollection()->transform(function ($attention) {
            $attention->elements = $attention->getElements($attention->id);
            $attention->details = $attention->getDetails($attention->id);

            return $attention;
        });

        // Devolvemos la colección transformada como respuesta JSON
        return response()->json($objects);
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
        if (!$object) {
            return response()->json(['message' => 'Attention not found'], 404);
        }

        $object = Attention::with(['worker.person', 'vehicle', 'vehicle.person', 'details', 'routeImages', 'driver', 'elements'])->find($id);
        $object->elements = $object->getElements($object->id);
        $object->details = $object->getDetails($object->id);

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

        if (!$attention) {
            return response()->json(['message' => 'Attention not found'], 404);
        }

        $object = Attention::with(['worker.person', 'vehicle', 'vehicle.person',
            'details', 'details.product.unit', 'routeImages', 'elements'])
            ->where('number', $number)->first();
        $object->elements = $object->getElements($object->id);

        $object->technicians = $object->technicians($object->id);

        return response()->json($object);
    }

    // /**
    //  * Create a new ATTENTION
    //  * @OA\Post(
    //  *     path="/tecnimotors-backend/public/api/attention",
    //  *     tags={"Attention"},
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         description="Attention data",
    //  *         @OA\MediaType(
    //  *             mediaType="multipart/form-data",
    //  *             @OA\Schema(
    //  *                 @OA\Property(property="arrivalDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
    //  *                 @OA\Property(property="deliveryDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
    //  *                 @OA\Property(property="observations", type="string", example="-"),
    //  *                 @OA\Property(property="fuelLevel", type="string", example="10"),
    //  *                 @OA\Property(property="km", type="string", example="0.00"),
    //  *                 @OA\Property(property="vehicle_id", type="integer", example=1),
    //  *                 @OA\Property(property="worker_id", type="integer", example=1),
    //  *                 @OA\Property(
    //  *                     property="details",
    //  *                     type="array",
    //  *                     @OA\Items(
    //  *                         type="object",
    //  *                         @OA\Property(property="service_id", type="integer", example=1),
    //  *                         @OA\Property(property="worker_id", type="integer", example=1)
    //  *                     )
    //  *                 ),
    //  *                 @OA\Property(
    //  *                     property="elements",
    //  *                     type="array",
    //  *                     @OA\Items(type="integer", example=1)
    //  *                 ),
    //  *                 @OA\Property(
    //  *                     property="detailsProducts",
    //  *                     type="array",
    //  *                     @OA\Items(type="integer", example=1)
    //  *                 ),
    //  *                 @OA\Property(
    //  *                     property="routeImage",
    //  *                     type="string",
    //  *                     format="binary",
    //  *                     description="Image file"
    //  *                 )
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Element created",
    //  *         @OA\JsonContent(ref="#/components/schemas/Attention")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=422,
    //  *         description="Validation error",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="error", type="string", example="The name has already been taken.")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthorized",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="message", type="string", example="Unauthenticated")
    //  *         )
    //  *     )
    //  * )
    //  */

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
     *             @OA\Property(property="fuelLevel", type="string", example="10"),
     *             @OA\Property(property="km", type="string", example="0.00"),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="worker_id", type="integer", example=1),
     * *     @OA\Property(property="driver_id", type="integer", example=1),
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
            'arrivalDate' => 'required',
            'deliveryDate' => 'required',
            'observations' => 'nullable',
            'fuelLevel' => 'required|in:0,2,4,6,8,10',
            'km' => 'required',
            'routeImage.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'vehicle_id' => 'required|exists:vehicles,id',
            'worker_id' => 'required|exists:workers,id',
            'driver_id' => 'nullable|exists:people,id',

            'elements' => 'nullable',
            'elements.*' => 'exists:elements,id',
            'details' => 'nullable',
            'detailsProducts' => 'nullable',

        ]);

        if (!$request->input('details')) {
            return response()->json(['error' => 'Atención sin Servicios'], 409);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $tipo = 'OTRS';
        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM attentions a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

        $data = [
            'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'arrivalDate' => $request->input('arrivalDate') ?? null,
            'deliveryDate' => $request->input('deliveryDate') ?? null,
            'observations' => $request->input('observations') ?? null,
            'typeofDocument' => $request->input('typeofDocument') ?? null,
            'fuelLevel' => $request->input('fuelLevel') ?? null,
            'km' => $request->input('km') ?? null,
            'routeImage' => 'ruta.jpg',
            'vehicle_id' => $request->input('vehicle_id') ?? null,
            'driver_id' => $request->input('driver_id') ?? null,

            'worker_id' => $request->input('worker_id') ?? null,
        ];

        $object = Attention::create($data);

        if ($object) {
            //ASIGNAR ELEMENTS
            $elements = $request->input('elements') ?? [];
            foreach ($elements as $element) {
                $objectData = [
                    'element_id' => $element,
                    'attention_id' => $object->id,
                ];
                ElementForAttention::create($objectData);
            }

            //ASIGNAR PRODUCTS
            $detailsProducts = $request->input('detailsProducts') ?? [];
            $sumProducts = 0;
            foreach ($detailsProducts as $productDetail) {
                $idProduct = $productDetail['idProduct'];
                $quantity = $productDetail['quantity'] ?? 1;

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
                    'attention_id' => $object->id,
                ];
                $detailProd = DetailAttention::create($objectData);
                $sumProducts += $detailProd->saleprice * $quantity;

            }
            $object->totalProducts = $sumProducts;

            //ASIGNAR DETAILS
            $detailsAttentions = $request->input('details') ?? [];
            $sumServices = 0;
            foreach ($detailsAttentions as $detail) {

                $service = Service::find($detail['service_id']);
                $objectData = [
                    'saleprice' => $service->saleprice ?? '0.00',
                    'type' => 'Service',
                    'comment' => $detail['comment'] ?? '-',
                    'status' => $detail['status'] ?? 'Generada',
                    'dateRegister' => Carbon::now(),
                    'dateMax' => $request->input('deliveryDate') ?? null,

                    'worker_id' => $detail['worker_id'],
                    'product_id' => $detail['product_id'] ?? null,
                    'service_id' => $detail['service_id'],
                    'attention_id' => $object->id,
                ];
                $detailService = DetailAttention::create($objectData);
                $sumServices += $detailService->saleprice;
            }

            $object->totalService = $sumServices;

        }
        $object->total = $object->totalService + $object->totalProducts;
        $object->save();

        //IMAGEN
        $images = $request->file('routeImage') ?? [];
        $index = 1;
        foreach ($images as $image) {

            $file = $image;
            $currentTime = now();
            $filename = $index . '-' . $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/photosSheetService', $filename);
            $rutaImagen = Storage::url($path);
            $object->routeImage = $rutaImagen;
            $object->save();
            $index++;
            $dataImage = [
                'route' => $rutaImagen,
                'attention_id' => $object->id,
            ];
            RouteImages::create($dataImage);
        }

        $object = Attention::with(['worker.person', 'vehicle', 'details', 'routeImages', 'driver'])->find($object->id);
        $object->elements = $object->getElements($object->id);
        $object->details = $object->getDetails($object->id);

        return response()->json($object);
    }

    /**
     * Update a attention
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
     *     @OA\Property(property="fuelLevel", type="string", example="2"),
     *     @OA\Property(property="km", type="string", example="0.00"),
     *     @OA\Property(property="driver_id", type="integer", example=1),
     *     @OA\Property(property="vehicle_id", type="string", example=1),
     *     @OA\Property(property="worker_id", type="string", example=1),
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
     *         @OA\JsonContent(ref="#/components/schemas/TypeAttention")
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
        if (!$object) {
            return response()->json(['message' => 'attention not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'arrivalDate' => 'required',
            'deliveryDate' => 'required',
            'observations' => 'nullable',
            'fuelLevel' => 'required',
            'km' => 'required',

            'vehicle_id' => 'required|exists:vehicles,id',
            'worker_id' => 'required|exists:workers,id',
            'driver_id' => 'nullable|exists:people,id',

            'elements' => 'nullable|array',
            'elements.*' => 'exists:elements,id',
            'details' => 'nullable',
            'detailsProducts' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'arrivalDate' => $request->input('arrivalDate') ?? null,
            'deliveryDate' => $request->input('deliveryDate') ?? null,
            'observations' => $request->input('observations') ?? null,
            'typeofDocument' => $request->input('typeofDocument') ?? null,
            'fuelLevel' => $request->input('fuelLevel') ?? null,
            'km' => $request->input('km') ?? null,
            'routeImage' => 'ruta.jpg',
            'vehicle_id' => $request->input('vehicle_id') ?? null,
            'driver_id' => $request->input('driver_id') ?? null,

            'worker_id' => $request->input('worker_id') ?? null,
        ];

        $object->update($data);

        $details = $request->input('elements', []);
        $object->setElements($object->id, $details);

        $detailsAt = $request->input('details', []);
        $object->setDetails($object->id, $detailsAt, $request->input('deliveryDate'));

        $detailsProducts = $request->input('detailsProducts') ?? [];
        $object->setDetailProducts($object->id, $detailsProducts);

        $object->total = $object->details()->get()->sum(function ($detail) {
            return $detail->saleprice * $detail->quantity;
        });
        $object->save();

        $images = $request->file('routeImage') ?? [];
        $object->setImages($object->id, $images);

        $object = Attention::with(['worker.person', 'vehicle', 'details', 'routeImages', 'driver'])->find($object->id);
        $object->elements = $object->getElements($object->id);
        $object->details = $object->getDetails($object->id);

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
        // VALIDAR AUN ESTA ELIMINACIÓN

        $object = Attention::find($id);
        if (!$object) {
            return response()->json(['message' => 'Attention not found'], 404);
        }
        $detailsNotGenerated = $object->details()->where('type', 'Service')
            ->where('status', '!=', 'Generada')->exists();

        if ($detailsNotGenerated) {
            return response()->json(['message' => 'Exiten Servicios que ya estan siendo Procesados'], 409);
        }

        $object->delete();

        return response()->json(['message' => 'Attention deleted']);
    }
}
