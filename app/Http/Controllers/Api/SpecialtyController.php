<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialtyController extends Controller
{
    /**
     * Get all Specialties
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/specialty",
     *     tags={"Specialty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Specialties",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Specialty")
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
        return response()->json(Specialty::simplePaginate(15));

    }

    /**
     * Show the specified Specialty
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/specialty/{id}",
     *     tags={"Specialty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Specialty",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specialty found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Specialty not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Specialty not found"
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

        $object = Specialty::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Specialty not found'], 404
        );

    }

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/specialty",
     *      summary="Store a new specialty",
     *      tags={"Specialty"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"person_id"},
     *              @OA\Property(property="name", type="string", example="Especialidad 1", description="Name"),
     *
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(ref="#/components/schemas/Specialty")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     * )
     */

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('specialties')->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $object = Specialty::create($data);
        $object = Specialty::find($object->id);
        return response()->json($object, 200);

    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/specialty/{id}",
     *     summary="Update specialty by ID",
     *     tags={"Specialty"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of specialty",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"person_id"},
     *              @OA\Property(property="name", type="string", example="Espcialidad 2", description="Name"),
     *
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/Specialty")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),

     * )
     *
     */
    public function update(Request $request, string $id)
    {

        $object = Specialty::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'Specialty not found'], 404
            );
        }
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('specialties')->ignore($id)->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $object->update($data);
        $object = Specialty::find($object->id);
        return response()->json($object, 200);
    }

    /**
     * Remove the specified Specialty
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/specialty/{id}",
     *     tags={"Specialty"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Specialty",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specialty deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Specialty deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Specialty not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Specialty not found"
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
        $object = Specialty::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Specialty not found'], 404
            );
        }

        if ($object->workers()->count() > 0) {
            return response()->json(
                ['message' => 'Specialty have Relation with workers'], 404
            );
        }

        //REVISAR ASOCIACIONES
        $object->delete();
    }
}
