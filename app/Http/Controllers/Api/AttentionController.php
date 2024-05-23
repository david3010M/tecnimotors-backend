<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\Element;
use App\Models\ElementForAttention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $object = Attention::simplePaginate(15);
        $object->getCollection()->transform(function ($typeUser) {
            $typeUser->elements = $typeUser->getElements($typeUser->id);
            return $typeUser;
        });
        return response()->json($object);

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
        $object = Attention::with(['worker', 'vehicle'])->find($id);
        $object->elements = $object->getElements($object->id);
        if (!$object) {
            return response()->json(['message' => 'Attention not found'], 404);
        }

        return response()->json($object);
    }

    /**
     * Create a new Element
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/attention",
     *     tags={"Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","worker_id"},
     *      @OA\Property(property="arrivalDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *      @OA\Property(property="deliveryDate", type="string", format="date-time", example="2024-03-13", description="Date Attention"),
     *      @OA\Property(property="observations", type="string", example="-"),

     *     @OA\Property(property="fuelLevel", type="string", example="Empty"),
     *     @OA\Property(property="km", type="string", example="0.00"),
     *     @OA\Property(property="routeImage", type="string", example="-"),
     *     @OA\Property(property="vehicle_id", type="string", example=1),
     *     @OA\Property(property="worker_id", type="string", example=1),
     *     @OA\Property(
     *                 property="details",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *       @OA\Property(property="service_id", type="integer", example=1),
     * @OA\Property(property="worker_id", type="integer", example=1),
     *                  ),
     *     ),
     *     @OA\Property(
     *                 property="elements",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                  ),
     *     ),
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
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'arrivalDate' => 'required',
            'deliveryDate' => 'required',
            'observations' => 'required',

            'fuelLevel' => 'required',
            'km' => 'required',
            'routeImage' => 'required',
            'vehicle_id' => 'required|exists:vehicles,id',
            'worker_id' => 'required|exists:workers,id',
            'elements' => 'nullable|array',
            'elements.*' => 'exists:elements,id',
            'details' => 'required|array|min:1',
            // 'details.*' => 'exists:details,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $tipo = 'ATTC';
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
            'routeImage' => $request->input('routeImage') ?? null,
            'vehicle_id' => $request->input('vehicle_id') ?? null,
            'worker_id' => $request->input('worker_id') ?? null,
        ];

        $object = Attention::create($data);

        if ($object) {
            //ASIGNAR ELEMENTS
            $details = $request->input('elements');

            foreach ($details as $detail) {

                $objectData = [
                    'element_id' => $detail['id'],
                    'attention_id' => $object->id,
                ];
                ElementForAttention::create($objectData)->id;

            }

        }

        $object = Attention::with(['worker', 'vehicle'])->find($object->id);
        $object->elements = $object->getElements($object->id);

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

     *     @OA\Property(property="fuelLevel", type="string", example="Empty"),
     *     @OA\Property(property="km", type="string", example="0.00"),
     *     @OA\Property(property="routeImage", type="string", example="-"),
     *     @OA\Property(property="vehicle_id", type="string", example=1),
     *     @OA\Property(property="worker_id", type="string", example=1),
     *    @OA\Property(
     *                  property="elements",
     *                  type="array",
     *                  @OA\Items(type="integer"),
     *                  example={1, 2, 3}
     *              )
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
            'observations' => 'required',

            'fuelLevel' => 'required',
            'km' => 'required',
            'routeImage' => 'required',
            'vehicle_id' => 'required|exists:vehicles,id',
            'worker_id' => 'required|exists:workers,id',

            'elements' => 'nullable|array',
            'elements.*' => 'exists:elements,id',
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
            'routeImage' => $request->input('routeImage') ?? null,
            'vehicle_id' => $request->input('vehicle_id') ?? null,
            'worker_id' => $request->input('worker_id') ?? null,
        ];

        $object->update($data);

        $details = $request->input('elements', []);
   
        $object->setElements($object->id, $details);
            

        $object = Attention::with(['worker', 'vehicle'])->find($object->id);
        $object->elements = $object->getElements($object->id);

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

//         $object = Attention::find($id);
//         if (!$object) {
//             return response()->json(['message' => 'Attention not found'], 404);
//         }

//         $object->delete();

        return response()->json(['message' => 'Attention deleted']);
    }
}
