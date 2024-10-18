<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashRequest;
use App\Http\Requests\UpdateCashRequest;
use App\Models\Cash;

class CashController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/cash",
     *     tags={"Cash"},
     *     summary="Get all cash",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response( response=200, description="Get all cash", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Cash"))),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=404, description="Not found"),
     * )
     */
    public function index()
    {
        return Cash::with('branch')->get();
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/cash",
     *     tags={"Cash"},
     *     summary="Store cash",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( required=true, description="Cash information",
     *         @OA\JsonContent( required={"name", "branch_id"},
     *             @OA\Property( property="name", type="string", example="Caja 1" ),
     *             @OA\Property( property="branch_id", type="integer", example="1" )
     *         )
     *     ),
     *     @OA\Response( response=200, description="Store cash", @OA\JsonContent(ref="#/components/schemas/Cash")),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=422, description="The given data was invalid")
     * )
     */
    public function store(StoreCashRequest $request)
    {
        $cash = Cash::create($request->validated());
        $cash = Cash::find($cash->id);
        return response()->json($cash);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/cash/{id}",
     *     tags={"Cash"},
     *     summary="Get cash",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Cash id", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Get cash", @OA\JsonContent(ref="#/components/schemas/Cash")),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        $cash = Cash::with('branch')->find($id);
        if (!$cash) return response()->json(['message' => 'Cash not found'], 404);
        return response()->json($cash);
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/cash/{id}",
     *     tags={"Cash"},
     *     summary="Update cash",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Cash id", @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, description="Cash information",
     *         @OA\JsonContent( required={"name", "branch_id"},
     *             @OA\Property( property="name", type="string", example="Caja 1" ),
     *             @OA\Property( property="branch_id", type="integer", example="1" )
     *         )
     *     ),
     *     @OA\Response( response=200, description="Update cash", @OA\JsonContent(ref="#/components/schemas/Cash")),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=404, description="Not found"),
     *     @OA\Response( response=422, description="The given data was invalid")
     * )
     */
    public function update(UpdateCashRequest $request, int $id)
    {
        $cash = Cash::find($id);
        if (!$cash) return response()->json(['message' => 'Cash not found'], 404);
        $cash->update($request->validated());
        $cash = Cash::find($cash->id);
        return response()->json($cash);
    }

    /**
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/cash/{id}",
     *     tags={"Cash"},
     *     summary="Delete cash",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Cash id", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Cash deleted"),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=404, description="Not found")
     * )
     */
    public function destroy(int $id)
    {
        $cash = Cash::find($id);
        if (!$cash) return response()->json(['message' => 'Cash not found'], 404);
        $cash->delete();
        return response()->json(['message' => 'Cash deleted']);
    }
}
