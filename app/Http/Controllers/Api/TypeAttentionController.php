<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeAttention;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypeAttentionController extends Controller
{

    /**
     * Get all Type Attentions
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeattention",
     *     tags={"Type Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Type Attentions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TypeAttention")
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
        return response()->json(TypeAttention::simplePaginate(15));
    }

    /**
     * Create a new Type Attention
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/typeattention",
     *     tags={"Type Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Consulta"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Attention created successfully",
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
            'name' => [
                'required',
                'string',
                Rule::unique('type_attentions')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $typeAttention = TypeAttention::create($data);
        $typeAttention = TypeAttention::find($typeAttention->id);

        return response()->json($typeAttention);
    }

    /**
     * Show a Type Attention
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeattention/{id}",
     *     tags={"Type Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Type Attention",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Attention found",
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
     *         description="Type Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Attention not found.")
     *         )
     *     )
     * )
     *
     */
    public function show(int $id)
    {
        $typeAttention = TypeAttention::find($id);
        if (!$typeAttention) {
            return response()->json(['message' => 'Type Attention not found.'], 404);
        }
        return response()->json($typeAttention);

    }

    /**
     * Update a Type Attention
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/typeattention/{id}",
     *     tags={"Type Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Type Attention",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Consulta"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Attention updated successfully",
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
     *         description="Type Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Attention not found.")
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
        $typeAttention = TypeAttention::find($id);
        if (!$typeAttention) {
            return response()->json(['message' => 'Type Attention not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('type_attentions')->ignore($id)->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $typeAttention->update($data);
        $typeAttention = TypeAttention::find($typeAttention->id);

        return response()->json($typeAttention);
    }

    /**
     * Delete a Type Attention
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/typeattention/{id}",
     *     tags={"Type Attention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Type Attention",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type Attention deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Attention deleted successfully.")
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
     *         description="Type Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type Attention not found.")
     *         )
     *     )
     * )
     *
     */
    public function destroy(int $id)
    {
        $typeAttention = TypeAttention::find($id);
        if (!$typeAttention) {
            return response()->json(['message' => 'Type Attention not found.'], 404);
        }

//        if ($typeAttention->attentions()->count() > 0) {
//            return response()->json(['message' => 'Type Attention has attentions associated.'], 409);
//        }

        $typeAttention->delete();
        return response()->json(['message' => 'Type Attention deleted successfully.']);
    }
}
