<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeUser;

class TypeUserController extends Controller
{
    /**
     * Get all TypeUsers
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeUser",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active TypeUsers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TypeUser")
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
        return response()->json(TypeUser::simplePaginate(15));
    }

    /**
     * Show the specified TypeUser
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeUser/{id}",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the TypeUser",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TypeUser found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
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

        $object = TypeUser::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'TypeUser not found'], 404
        );

    }

    /**
     * Remove the specified TypeUser
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/typeUser/{id}",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the TypeUser",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TypeUser deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
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
        $object = TypeUser::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'TypeUser not found'], 404
            );
        }
        if ($object->getAccess($id)->count() > 0) {
            return response()->json(
                ['message' => 'TypeUser has Access associated'], 409
            );
        }
        $object->delete();

    }
}
