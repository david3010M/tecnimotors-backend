<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommitmentResource;
use App\Models\Attention;
use App\Models\budgetSheet;
use App\Models\Commitment;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    /**
     * SHOW ALL COMMITMENTS
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/commitment",
     *     tags={"Commitment"},
     *     summary="Show all commitments",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Show all commitments", @OA\JsonContent(ref="#/components/schemas/CommitmentCollectionPagination")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     * )
     *
     */
    public function index(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'page' => 'integer',
            'per_page' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $per_page = $request->query('per_page', 5);

        $commitments = Commitment::with('budgetSheet.attention.vehicle.person', 'extensions')
            ->orderBy('id', 'desc')
            ->paginate($per_page);
        CommitmentResource::collection($commitments);
        return response()->json($commitments);
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/commitment",
     *     tags={"Commitment"},
     *     summary="Create a commitment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CommitmentRequest")
     *     ),
     *     @OA\Response(response="200", description="Create a commitment", @OA\JsonContent(ref="#/components/schemas/Commitment")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Budget sheet not found")
     *     ))
     * )
     *
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'amount' => 'required|numeric',
            'dues' => 'required|integer',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string|in:Semanal,Quincenal,Mensual',
            'budget_sheet_id' => 'required|integer|exists:budget_sheets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $budgetSheet = budgetSheet::find($request->input('budget_sheet_id'));

        $data = [
            'dues' => $request->input('dues'),
            'amount' => $request->input('amount'),
            'balance' => $budgetSheet->total - $request->input('amount'),
            'payment_date' => $request->input('payment_date'),
            'payment_type' => $request->input('payment_type'),
            'status' => 'Pendiente',
            'budget_sheet_id' => $request->input('budget_sheet_id'),
        ];

        $commitment = Commitment::create($data);

        $budgetSheet->debtAmount = $commitment->amount;
        $budgetSheet->save();

        $attention = Attention::find($budgetSheet->attention_id);
        $attention->debtAmount = $commitment->amount;
        $attention->save();

        $commitment = Commitment::find($commitment->id);

        return response()->json($commitment);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/commitment/{id}",
     *     tags={"Commitment"},
     *     summary="Show a commitment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="Commitment ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Show a commitment", @OA\JsonContent(ref="#/components/schemas/Commitment")),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Commitment not found")
     *     ))
     * )
     */
    public function show(int $id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json(['error' => 'Commitment not found'], 404);
        }

        return response()->json(new CommitmentResource($commitment));
    }

    /**
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/commitment/{id}",
     *     tags={"Commitment"},
     *     summary="Update a commitment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="Commitment ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CommitmentRequest")
     *     ),
     *     @OA\Response(response="200", description="Update a commitment", @OA\JsonContent(ref="#/components/schemas/Commitment")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Commitment not found")
     *     ))
     * )
     */
    public function update(Request $request, int $id)
    {
        $validator = validator()->make($request->all(), [
            'amount' => 'required|numeric',
            'dues' => 'required|integer',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string|in:Semanal,Quincenal,Mensual',
            'budget_sheet_id' => 'required|integer|exists:budget_sheets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json(['error' => 'Commitment not found'], 404);
        }

        $budgetSheet = budgetSheet::find($request->input('budget_sheet_id'));

        $data = [
            'dues' => $request->input('dues'),
            'amount' => $request->input('amount'),
            'balance' => $budgetSheet->total - $request->input('amount'),
            'payment_date' => $request->input('payment_date'),
            'payment_type' => $request->input('payment_type'),
            'status' => 'Pendiente',
            'budget_sheet_id' => $request->input('budget_sheet_id'),
        ];

        $commitment->update($data);

        $budgetSheet->debtAmount = $commitment->amount;
        $budgetSheet->save();

        $attention = Attention::find($budgetSheet->attention_id);
        $attention->debtAmount = $commitment->amount;
        $attention->save();

        $commitment = Commitment::find($commitment->id);

        return response()->json($commitment);

    }

    /**
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/commitment/{id}",
     *     tags={"Commitment"},
     *     summary="Delete a commitment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="Commitment ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Delete a commitment", @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="Commitment deleted")
     *     )),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Commitment not found")
     *     ))
     * )
     */
    public function destroy(int $id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json(['error' => 'Commitment not found'], 404);
        }

        $commitment->delete();

        return response()->json(['message' => 'Commitment deleted']);
    }
}
