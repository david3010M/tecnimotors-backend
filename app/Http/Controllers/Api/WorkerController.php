<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Specialty;
use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    /**
     * Get all Workers
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/worker",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Workers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Worker")
     *         )
     *     ),
     *       @OA\Parameter(
     *         name="speciality_id",
     *         in="query",
     *         description="ID of the Specialty to filter Workers",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *       @OA\Parameter(
     *         name="occupation",
     *         in="query",
     *         description="Occupation of the Workers",
     *         required=false,
     *               example="Mecanico"
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

    public function index(Request $request)
    {
        $speciality_id = $request->input('speciality_id');
        $occupation = $request->input('occupation');

        if (!$speciality_id) {
            return response()->json(Worker::whereRaw('LOWER(occupation) = ?', [strtolower($occupation)])
                    ->with(['person'])->simplePaginate(25));
        } else {
            $object = Specialty::find($speciality_id);
            if (!$object) {
                return response()->json(
                    ['message' => 'Specialty not found'], 404
                );
            }

            $workers = Worker::whereHas('specialties', function ($query) use ($occupation, $speciality_id) {
                $query->where('specialties.id', $speciality_id);
                if ($occupation) {
                    $query->whereRaw('LOWER(occupation) = ?', [strtolower($occupation)]);
                }
            })->with(['person'])->simplePaginate(25);

            return response()->json($workers);
        }
    }

    /**
     * Show the specified Worker
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Worker",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker not found"
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

        $object = Worker::with(['person', 'specialties'])->find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Worker not found'], 404
        );

    }

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/worker",
     *      summary="Store a new worker",
     *      tags={"Worker"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"person_id"},
     *              @OA\Property(property="startDate", type="string", example=null, description="StartDate of the worker"),
     *              @OA\Property(property="birthDate", type="string", example=null, description="BirthDate of the worker"),
     *               @OA\Property(property="occupation", type="string", example="-", description="Occupationi of the worker"),
     *              @OA\Property(property="person_id", type="integer", example="1", description="Worker ID")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(ref="#/components/schemas/Worker")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      )
     * )
     */

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'person_id' => 'required|exists:people,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'startDate' => $request->input('startDate'),
            'birthDate' => $request->input('birthDate'),
            'occupation' => $request->input('occupation'),
            'person_id' => $request->input('person_id'),

        ];

        $object = Worker::create($data);
        $object = Worker::with(['person'])->find($object->id);
        return response()->json($object, 200);

    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     summary="Update worker by ID",
     *     tags={"Worker"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of worker",
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
     *              @OA\Property(property="startDate", type="string", example=null, description="StartDate of the worker"),
     *              @OA\Property(property="birthDate", type="string", example=null, description="BirthDate of the worker"),
     *               @OA\Property(property="occupation", type="string", example="-", description="Occupationi of the worker"),
     *              @OA\Property(property="person_id", type="integer", example="1", description="Worker ID")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/Worker")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User  not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
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

        $object = Worker::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }
        $validator = validator()->make($request->all(), [
            'person_id' => 'required|exists:people,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'startDate' => $request->input('startDate'),
            'birthDate' => $request->input('birthDate'),
            'occupation' => $request->input('occupation'),
            'person_id' => $request->input('person_id'),
        ];

        $object->update($data);
        $object = Worker::with(['person'])->find($object->id);
        return response()->json($object, 200);
    }

    /**
     * Remove the specified Worker
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Worker",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker not found"
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
        $object = Worker::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Worker not found'], 404
            );
        }

        //REVISAR ASOCIACIONES
        $object->delete();
    }
}
