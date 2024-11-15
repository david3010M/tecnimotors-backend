<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestGuideMotive;
use App\Http\Resources\GuideMotiveResource;
use App\Models\GuideMotive;
use App\Http\Requests\StoreGuideMotiveRequest;
use App\Http\Requests\UpdateGuideMotiveRequest;

class GuideMotiveController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/guide-motives",
     *     tags={"GuideMotive"},
     *     summary="Get all guide motives",
     *     description="Get all guide motives",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GuideMotiveResource")
     *         )
     *     ),
     *     @OA\Response( response=401, description="Unauthenticated" ),
     *     @OA\Response( response=403, description="Forbidden" ),
     *     @OA\Response( response=404, description="Resource Not Found" ),
     *     @OA\Response( response=500, description="Internal Server Error" )
     * )
     *
     */
    public function index()
    {
        return response()->json(GuideMotiveResource::collection(GuideMotive::all()));
    }

    public function store(StoreGuideMotiveRequest $request)
    {
        //
    }

    public function show(int $id)
    {
        //
    }

    public function update(UpdateGuideMotiveRequest $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
