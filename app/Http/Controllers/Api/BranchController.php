<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/branch",
     *     tags={"Branches"},
     *     summary="Get all branches",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response( response=200, description="Get all branches", @OA\JsonContent( type="array", @OA\Items(ref="#/components/schemas/Branch"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response( response=404, description="Not Found")
     * )
     */
    public function index()
    {
        return Branch::all();
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/branch",
     *     tags={"Branches"},
     *     summary="Create a branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent( required={"name"}, @OA\Property( property="name", type="string", example="Tecnimotors del Perú"))),
     *     @OA\Response( response=200, description="Create a branch", @OA\JsonContent(ref="#/components/schemas/Branch")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(StoreBranchRequest $request)
    {
        $branch = Branch::create($request->validated());
        $branch = Branch::find($branch->id);
        return response()->json($branch);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/branch/{id}",
     *     tags={"Branches"},
     *     summary="Get a branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Branch ID", @OA\Schema( type="integer")),
     *     @OA\Response( response=200, description="Get a branch", @OA\JsonContent(ref="#/components/schemas/Branch")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response( response=404, description="Not Found")
     * )
     */
    public function show(int $id)
    {
        $branch = Branch::find($id);
        if (!$branch) return response()->json(['error' => 'Branch not found'], 404);
        return response()->json($branch);
    }

    /**
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/branch/{id}",
     *     tags={"Branches"},
     *     summary="Update a branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Branch ID", @OA\Schema( type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent( required={"name"}, @OA\Property( property="name", type="string", example="Tecnimotors del Perú"))),
     *     @OA\Response( response=200, description="Update a branch", @OA\JsonContent(ref="#/components/schemas/Branch")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response( response=404, description="Not Found")
     * )
     */
    public function update(UpdateBranchRequest $request, int $id)
    {
        $branch = Branch::find($id);
        if (!$branch) return response()->json(['error' => 'Branch not found'], 404);
        $branch->update($request->validated());
        return response()->json($branch);
    }

    /**
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/branch/{id}",
     *     tags={"Branches"},
     *     summary="Delete a branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="Branch ID", @OA\Schema( type="integer")),
     *     @OA\Response( response=200, description="Branch deleted", @OA\JsonContent( type="string", example="Branch deleted")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response( response=404, description="Not Found"),
     *     @OA\Response( response=409, description="Conflict", @OA\JsonContent( type="string", example="Branch has cashes"))
     * )
     */
    public function destroy(int $id)
    {
        $branch = Branch::find($id);
        if (!$branch) return response()->json(['error' => 'Branch not found'], 404);
        if ($branch->cashes()->count() > 0) return response()->json(['error' => 'Branch has cashes'], 409);
        $branch->delete();
        return response()->json(['message' => 'Branch deleted']);
    }
}
