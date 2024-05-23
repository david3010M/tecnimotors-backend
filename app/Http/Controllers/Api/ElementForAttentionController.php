<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElementForAttention;
use Illuminate\Http\Request;

class ElementForAttentionController extends Controller
{
    /**
     * Get all Elements
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/elementForAttention",
     *     tags={"ElementForAttention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Elements",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ElementForAttention")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/elementForAttention?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/elementForAttention?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/elementForAttention"),
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
        return response()->json(ElementForAttention::with(['element', 'attention'])->simplePaginate(15));
    }
    /**
     * Get a single Element
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/elementForAttention/{id}",
     *     tags={"ElementForAttention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Element ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element data",
     *         @OA\JsonContent(ref="#/components/schemas/ElementForAttention")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Element not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Element not found")
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
        $element = ElementForAttention::with(['element', 'attention'])->find($id);
        if (!$element) {
            return response()->json(['message' => 'Element For Attention not found'], 404);
        }

        return response()->json($element);
    }

    /**
     * Create a new Element
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/elementForAttention",
     *     tags={"ElementForAttention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"element_id","attention_id"},
     *     @OA\Property(property="element_id", type="integer", example=1),
     *     @OA\Property(property="attention_id", type="integer", example=1),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element created",
     *         @OA\JsonContent(ref="#/components/schemas/ElementForAttention")
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
            'element_id' => 'required|exists:elements,id',
            'attention_id' => 'required|exists:attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'element_id' => $request->input('element_id') ?? null,
            'attention_id' => $request->input('attention_id') ?? null,
        ];

        $attention = ElementForAttention::create($data);
        $attention = ElementForAttention::with(['element', 'attention'])->find($attention->id);

        return response()->json($attention);
    }

    /**
     * Update a elementForAttention
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/elementForAttention/{id}",
     *     tags={"ElementForAttention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Element For Attention",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"element_id","attention_id"},
     *     @OA\Property(property="element_id", type="integer", example=1),
     *     @OA\Property(property="attention_id", type="integer", example=1),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="elementForAttention updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ElementForAttention")
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
     *         description="elementForAttention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="elementForAttention not found.")
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
        $elementFoAttention = ElementForAttention::find($id);
        if (!$elementFoAttention) {
            return response()->json(['message' => 'Element For attention not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'element_id' => 'required|exists:elements,id',
            'attention_id' => 'required|exists:attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'element_id' => $request->input('element_id') ?? null,
            'attention_id' => $request->input('attention_id') ?? null,
        ];

        $elementFoAttention->update($data);
        $elementFoAttention = ElementForAttention::with(['element', 'attention'])->find($elementFoAttention->id);

        return response()->json($elementFoAttention);
    }

    /**
     * Delete an Element
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/elementForAttention/{id}",
     *     tags={"ElementForAttention"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Element ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Element deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Element not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Element not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Element has elements for attention",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Element has elements for attention")
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
        $element = ElementForAttention::find($id);
        if (!$element) {
            return response()->json(['message' => 'Element For Attention not found'], 404);
        }

//        if ($element->elementsForAttention()->count() > 0) {
//            return response()->json(['message' => 'Element For Attention has elements for attention'], 409);
//        }

        $element->delete();

        return response()->json(['message' => 'Element For Attention deleted']);
    }
}
