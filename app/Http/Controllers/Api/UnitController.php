<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Get all units
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/unit",
     *     tags={"Unit"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property (property="current_page", type="integer", example="1"),
     *             @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/Unit")),
     *             @OA\Property (property="first_page_url", type="string", example="http://localhost:8000/api/unit?page=1"),
     *             @OA\Property (property="from", type="integer", example="1"),
     *             @OA\Property (property="next_page_url", type="string", example="null"),
     *             @OA\Property (property="path", type="string", example="http://localhost:8000/api/unit"),
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
        return response()->json(Unit::simplePaginate(15));
    }

    /**
     * Create a new unit
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/unit",
     *     tags={"Unit"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UnitRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
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
                Rule::unique('units', 'name')->whereNull('deleted_at')
            ],
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'code' => $request->input('code'),
        ];

        $unit = Unit::create($data);
        $unit = Unit::find($unit->id);

        return response()->json($unit);

    }

    /**
     * Get a unit by id
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Unit id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
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
        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($unit);
    }

    /**
     * Update a unit
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Unit id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UnitRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
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
        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('units')->whereNull('deleted_at')->ignore($unit->id)
            ],
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'code' => $request->input('code'),
        ];

        $unit->update($data);
        $unit = Unit::find($unit->id);

        return response()->json($unit);
    }

    /**
     * Delete a unit
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Unit id",
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
     *             @OA\Property (property="error", type="string", example="Cannot delete unit with products")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json(['error' => 'Not found'], 404);
        }

//        if ($unit->products()->count() > 0) {
//            return response()->json(['error' => 'Cannot delete unit with products'], 409);
//        }

        $unit->delete();

        return response()->json(['message' => 'Unit deleted']);
    }
}
