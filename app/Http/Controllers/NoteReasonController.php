<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestNoteReason;
use App\Http\Resources\NoteReasonResource;
use App\Models\NoteReason;
use App\Http\Requests\StoreNoteReasonRequest;
use App\Http\Requests\UpdateNoteReasonRequest;

class NoteReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/noteReason",
     *     tags={"Note Reason"},
     *     summary="Get all note reasons",
     *     description="Get all note reasons",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="code", in="query", description="Filter note reasons", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filter note reasons", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", description="Sort note reasons", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/NoteReasonCollection")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function index(IndexRequestNoteReason $request)
    {
        return $this->getFilteredResults(
            NoteReason::class,
            $request,
            NoteReason::filters,
            NoteReason::sorts,
            NoteReasonResource::class
        );
    }

    public function store(StoreNoteReasonRequest $request)
    {
        $noteReason = NoteReason::create($request->validated());
        $noteReason = NoteReason::find($noteReason->id);
        return response()->json(new NoteReasonResource($noteReason));
    }

    public function show(int $id)
    {
        $noteReason = NoteReason::find($id);
        if (!$noteReason) return response()->json(['error' => 'Note reason not found'], 404);
        return response()->json(new NoteReasonResource($noteReason));
    }

    public function update(UpdateNoteReasonRequest $request, int $id)
    {
        $noteReason = NoteReason::find($id);
        if (!$noteReason) return response()->json(['error' => 'Note reason not found'], 404);
        $noteReason->update($request->validated());
        $noteReason = NoteReason::find($noteReason->id);
        return response()->json(new NoteReasonResource($noteReason));
    }

    public function destroy(int $id)
    {
        $noteReason = NoteReason::find($id);
        if (!$noteReason) return response()->json(['error' => 'Note reason not found'], 404);
        if ($noteReason->notes()->count() > 0) return response()->json(['error' => 'Note reason has notes'], 409);
        $noteReason->delete();
        return response()->json(new NoteReasonResource($noteReason));
    }
}
