<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeVehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypeVehicleController extends Controller
{
    /**
     * Get all Type Vehicles
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeVehicle",
     *     tags={"Type Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Type Vehicles",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypeVehicle")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/typevehicle?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/typevehicle?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/typevehicle"),
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
        return response()->json(TypeVehicle::simplePaginate(15));
    }

    /**
     * Create a new Type Vehicle
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/typeVehicle",
     *     tags={"Type Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Carro"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Vehicle created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TypeVehicle")
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
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name has already been taken.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('type_vehicles')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $typeVehicle = TypeVehicle::create($data);
        $typeVehicle = TypeVehicle::find($typeVehicle->id);

        return response()->json($typeVehicle);
    }

    /**
     * Show a Type Vehicle
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeVehicle/{id}",
     *     tags={"Type Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of Type Vehicle",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Vehicle found",
     *         @OA\JsonContent(ref="#/components/schemas/TypeVehicle")
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
     *         description="Type Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Vehicle not found")
     *         )
     *     )
     * )
     *
     */
    public function show(int $id)
    {
        $typeVehicle = TypeVehicle::find($id);
        if (!$typeVehicle) {
            return response()->json(['message' => 'Type Vehicle not found'], 404);
        }
        return response()->json($typeVehicle);
    }

    /**
     * Update a Type Vehicle
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/typeVehicle/{id}",
     *     tags={"Type Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of Type Vehicle",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Moto"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Vehicle updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TypeVehicle")
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
     *         description="Type Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Vehicle not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name has already been taken.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $typeVehicle = TypeVehicle::find($id);
        if (!$typeVehicle) {
            return response()->json(['message' => 'Type Vehicle not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('type_vehicles')->ignore($id)->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $typeVehicle->update($data);
        $typeVehicle = TypeVehicle::find($typeVehicle->id);

        return response()->json($typeVehicle);
    }

    /**
     * Delete a Type Vehicle
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/typeVehicle/{id}",
     *     tags={"Type Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of Type Vehicle",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Vehicle deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Vehicle deleted successfully.")
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
     *         description="Type Vehicle not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Vehicle not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Vehicle cannot be deleted because it has related vehicles.")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $typeVehicle = TypeVehicle::find($id);
        if (!$typeVehicle) {
            return response()->json(['message' => 'Type Vehicle not found.'], 404);
        }

//        if ($typeVehicle->vehicles()->count() > 0) {
//            return response()->json(['message' => 'Type Vehicle cannot be deleted because it has related vehicles.'], 409);
//        }

        $typeVehicle->delete();
        return response()->json(['message' => 'Type Vehicle deleted successfully.']);
    }
}
