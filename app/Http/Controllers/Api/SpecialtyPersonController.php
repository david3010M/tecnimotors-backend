<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpecialtyPerson;
use Illuminate\Http\Request;

class SpecialtyPersonController extends Controller
{
    /**
     * Get all Specialties
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/specialtyPerson",
     *     tags={"SpecialtyPerson"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Specialties",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SpecialtyPerson")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        return response()->json(SpecialtyPerson::with(['specialty', 'worker'])->simplePaginate(15));

    }
    /**
     * Show the specified SpecialtyPerson
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/SpecialtyPerson/{id}",
     *     tags={"SpecialtyPerson"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the SpecialtyPerson",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SpecialtyPerson found",
     * @OA\JsonContent(ref="#/components/schemas/SpecialtyPerson")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SpecialtyPerson not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="SpecialtyPerson not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function show(int $id)
    {

        $object = SpecialtyPerson::with(['specialty', 'worker'])->find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'SpecialtyPerson not found'], 404
        );

    }

    /**
     * Create a new SpecialtyPerson
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/specialtyPerson",
     *     tags={"SpecialtyPerson"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *             @OA\Property(property="specialty_id", type="integer", example="1", description="Specialty ID"),
     * @OA\Property(property="worker_id", type="integer", example="1", description="Worker ID")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SpecialtyPerson created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SpecialtyPerson")
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
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'specialty_id' => 'required|exists:specialties,id',
            'worker_id' => 'required|exists:workers,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'specialty_id' => $request->input('specialty_id'),
            'worker_id' => $request->input('worker_id'),
        ];

        $object = SpecialtyPerson::create($data);
        $object = SpecialtyPerson::with(['specialty', 'worker'])->find($object->id);

        return response()->json($object);
    }

    /**
     * Update a Specialty Person
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/specialtyPerson/{id}",
     *     tags={"SpecialtyPerson"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Specialty Person",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *             @OA\Property(property="specialty_id", type="integer", example="1", description="Specialty ID"),
     * @OA\Property(property="worker_id", type="integer", example="1", description="Worker ID")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specialty Person updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SpecialtyPerson")
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
     *         description="Specialty Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Specialty Person not found.")
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
        $specialtyPerson = SpecialtyPerson::find($id);
        if (!$specialtyPerson) {
            return response()->json(['message' => 'Specialty Person not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'specialty_id' => 'required|exists:specialties,id',
            'worker_id' => 'required|exists:workers,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'specialty_id' => $request->input('specialty_id'),
            'worker_id' => $request->input('worker_id'),
        ];

        $specialtyPerson->update($data);
        $specialtyPerson = SpecialtyPerson::with(['specialty', 'worker'])->find($specialtyPerson->id);

        return response()->json($specialtyPerson);
    }

    /**
     * Remove the specified SpecialtyPerson
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/SpecialtyPerson/{id}",
     *     tags={"SpecialtyPerson"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the SpecialtyPerson",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SpecialtyPerson deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="SpecialtyPerson deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SpecialtyPerson not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="SpecialtyPerson not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),

     * )
     *
     */
    public function destroy(int $id)
    {
        $object = SpecialtyPerson::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'SpecialtyPerson not found'], 404
            );
        }

        if ($object->worker) {
            return response()->json(
                ['message' => 'SpecialtyPerson have Relation with worker'], 404
            );
        }

        //REVISAR ASOCIACIONES
        $object->delete();
    }
}
