<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worker;

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
        return response()->json(Worker::with(['person'])->simplePaginate(15));
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

        $object = Worker::with(['person'])->find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Worker not found'], 404
        );

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
        $object = Worker::with(['person'])->find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Worker not found'], 404
            );
        }
        $object->delete();
    }
}
