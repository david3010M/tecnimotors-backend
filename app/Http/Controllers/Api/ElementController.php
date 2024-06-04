<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Element;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ElementController extends Controller
{
    /**
     * Get all Elements
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/element",
     *     tags={"Element"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Elements",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Element")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/element?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/element?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/element"),
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
        return response()->json(Element::simplePaginate(30));
    }

    /**
     * Create a new Element
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/element",
     *     tags={"Element"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Element 1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element created",
     *         @OA\JsonContent(ref="#/components/schemas/Element")
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
            'name' => [
                'required',
                'string',
                Rule::unique('elements')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $element = Element::create($data);
        $element = Element::find($element->id);

        return response()->json($element);
    }

    /**
     * Get a single Element
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/element/{id}",
     *     tags={"Element"},
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
     *         @OA\JsonContent(ref="#/components/schemas/Element")
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
        $element = Element::find($id);
        if (!$element) {
            return response()->json(['message' => 'Element not found'], 404);
        }

        return response()->json($element);
    }

    /**
     * Update an Element
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/element/{id}",
     *     tags={"Element"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Element ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Element 2"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Element updated",
     *         @OA\JsonContent(ref="#/components/schemas/Element")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Element not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Element not found")
     *         )
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
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $element = Element::find($id);
        if (!$element) {
            return response()->json(['message' => 'Element not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('elements')->ignore($id)->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name')
        ];

        $element->update($data);
        $element = Element::find($element->id);

        return response()->json($element);
    }

    /**
     * Delete an Element
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/element/{id}",
     *     tags={"Element"},
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
        $element = Element::find($id);
        if (!$element) {
            return response()->json(['message' => 'Element not found'], 404);
        }

        $element->delete();

        return response()->json(['message' => 'Element deleted']);
    }
}
