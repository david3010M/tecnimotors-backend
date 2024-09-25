<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ocupation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OcupationController extends Controller
{
        /**
     * Get all ocupations
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/ocupation",
     *     tags={"Ocupation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property (property="current_page", type="integer", example="1"),
     *             @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/Ocupation")),
     *             @OA\Property (property="first_page_url", type="string", example="http://localhost:8000/api/ocupation?page=1"),
     *             @OA\Property (property="from", type="integer", example="1"),
     *             @OA\Property (property="next_page_url", type="string", example="null"),
     *             @OA\Property (property="path", type="string", example="http://localhost:8000/api/ocupation"),
     *             @OA\Property (property="per_page", type="integer", example="15"),
     *             @OA\Property (property="prev_page_url", type="string", example="null"),
     *             @OA\Property (property="to", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Ocupation::simplePaginate(15));
    }

    /**
     * Create a new ocupation
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/ocupation",
     *     tags={"Ocupation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OcupationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Ocupation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="The name has already been taken.")
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
                Rule::unique('ocupations', 'name')->whereNull('deleted_at')
            ],
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'comment' => $request->input('comment'),
        ];

        $ocupation = Ocupation::create($data);
        $ocupation = Ocupation::find($ocupation->id);

        return response()->json($ocupation);

    }

    /**
     * Get a ocupation by id
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/ocupation/{id}",
     *     tags={"Ocupation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ocupation id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Ocupation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="Not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $ocupation = Ocupation::find($id);

        if (!$ocupation) {
            return response()->json(['error' => 'Ocupation Not found'], 404);
        }

        return response()->json($ocupation);
    }

    /**
     * Update a ocupation
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/ocupation/{id}",
     *     tags={"Ocupation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ocupation id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OcupationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Ocupation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="Not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="The name has already been taken.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        if (in_array($id, [1, 2, 3, 4, 5])) {
            return response()->json(['error' => 'No se puede editar este registro.'], 403);
        }

        $ocupation = Ocupation::find($id);

        if (!$ocupation) {
            return response()->json(['error' => 'Ocupation Not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('ocupations')->whereNull('deleted_at')->ignore($ocupation->id)
            ],
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'comment' => $request->input('comment'),
        ];

        $ocupation->update($data);
        $ocupation = Ocupation::find($ocupation->id);

        return response()->json($ocupation);
    }

    /**
     * Delete a ocupation
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/ocupation/{id}",
     *     tags={"Ocupation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ocupation id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property (property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="Not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property (property="error", type="string", example="Cannot delete ocupation with products")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        if (in_array($id, [1, 2, 3, 4, 5])) {
            return response()->json(['error' => 'No se puede eliminar este registro.'], 403);
        }
        
        $ocupation = Ocupation::find($id);

        if (!$ocupation) {
            return response()->json(['error' => 'Ocupation Not found'], 404);
        }

//        if ($ocupation->products()->count() > 0) {
//            return response()->json(['error' => 'Cannot delete ocupation with products'], 409);
//        }

        $ocupation->delete();

        return response()->json(['message' => 'Ocupation deleted']);
    }
}
