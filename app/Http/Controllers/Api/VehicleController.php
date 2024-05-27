<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Get all Vehicles
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/vehicle",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Vehicles",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/VehicleCollection")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/vehicle?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/vehicle?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/vehicle"),
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
        return response()->json(Vehicle::with('person', 'typeVehicle', 'brand')
            ->simplePaginate(15));
    }

    /**
     * Create a new Vehicle
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/vehicle",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VehicleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle created",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleCollection")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The plate field is required.")
     *         )
     *    )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'plate' => [
                'required',
                'string',
                Rule::unique('vehicles')->whereNull('deleted_at')
            ],
            'km' => 'required|numeric',
            'year' => 'required|numeric',
            'model' => 'required|string',
            'chasis' => 'required|string',
            'motor' => 'required|string',
            'person_id' => 'required|int',
            'typeVehicle_id' => 'required|int',
            'brand_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'plate' => $request->input('plate'),
            'km' => $request->input('km'),
            'year' => $request->input('year'),
            'model' => $request->input('model'),
            'chasis' => $request->input('chasis'),
            'motor' => $request->input('motor'),
            'person_id' => $request->input('person_id'),
            'typeVehicle_id' => $request->input('typeVehicle_id'),
            'brand_id' => $request->input('brand_id'),
        ];

        $vehicle = Vehicle::create($data);
        $vehicle = Vehicle::find($vehicle->id)->with('person', 'typeVehicle', 'brand')->first();

        return response()->json($vehicle);
    }

    /**
     * Get a Vehicle
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/vehicle/{id}",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Vehicle ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle data",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleCollection")
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
     *         description="Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehicle not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $vehicle = Vehicle::with('person', 'typeVehicle', 'brand')
            ->where('id', $id)
            ->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        return response()->json($vehicle);
    }

    /**
     * Update a Vehicle
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/vehicle/{id}",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Vehicle ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VehicleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle updated",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleCollection")
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
     *         description="Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehicle not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The plate field is required.")
     *         )
     *    )
     * )
     */
    public function update(Request $request, int $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'plate' => [
                'required',
                'string',
                Rule::unique('vehicles')->ignore($id)->whereNull('deleted_at')
            ],
            'km' => 'required|numeric',
            'year' => 'required|numeric',
            'model' => 'required|string',
            'chasis' => 'required|string',
            'motor' => 'required|string',
            'person_id' => 'required|int',
            'typeVehicle_id' => 'required|int',
            'brand_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'plate' => $request->input('plate'),
            'km' => $request->input('km'),
            'year' => $request->input('year'),
            'model' => $request->input('model'),
            'chasis' => $request->input('chasis'),
            'motor' => $request->input('motor'),
            'person_id' => $request->input('person_id'),
            'typeVehicle_id' => $request->input('typeVehicle_id'),
            'brand_id' => $request->input('brand_id'),
        ];

        $vehicle->update($data);
        $vehicle = Vehicle::find($vehicle->id)->with('person', 'typeVehicle', 'brand')->first();

        return response()->json($vehicle);
    }

    /**
     * Delete a Vehicle
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/vehicle/{id}",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Vehicle ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehicle deleted")
     *         )
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
     *         description="Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehicle not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehicle has attentions")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

//        if ($vehicle->attentions()->count() > 0) {
//            return response()->json(['message' => 'Vehicle has attentions'], 409);
//        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted']);
    }


    /**
     * Get all Vehicles by Person
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/vehicleByPerson/{id}",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of active Vehicles by Person",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleCollection")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Person not found")
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
     *
     */
    public function getVehiclesByPerson(int $id)
    {
        $person = Person::find($id);

        if (!$person) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        $vehicles = Vehicle::where('person_id', $id)->with('person', 'typeVehicle', 'brand')->get();
        return response()->json($vehicles);
    }
}
