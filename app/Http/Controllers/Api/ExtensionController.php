<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commitment;
use App\Models\Extension;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    /**
     * SHOW ALL COMMITMENTS
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/extension",
     *     tags={"Extension"},
     *     summary="Show all extensions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Show all extensions", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     * )
     *
     */
    public function index(Request $request)
    {

        $commitmentId = $request->input('commitment_id') ?? '';
    
        $extensions = Extension::when($commitmentId !== '', function ($query) use ($commitmentId) {
            return $query->where('commitment_id', $commitmentId);
        })->get();
    
        return response()->json($extensions);
    }
    

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/extension",
     *     tags={"Extension"},
     *     summary="Create an extension",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/ExtensionRequest")),
     *     @OA\Response(response="200", description="Create an extension", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'newEndDate' => 'required|date|after:commitment.payment_date',
            'reason' => 'required|string',
            'commitment_id' => 'required|integer|exists:commitments,id',
        ]);
        if ($validator->fails()) return response()->json(['error' => $validator->errors()->first()], 422);

        $commitment = Commitment::find($request->commitment_id);
        $commitmentDate = Carbon::parse($commitment->payment_date)->format('Y-m-d');
        $isAfter = Carbon::parse($request->newEndDate)->isAfter($commitmentDate);
        if (!$isAfter) return response()->json(['error' => 'La nueva fecha de pago ' . $request->newEndDate . ' debe ser posterior a la fecha de pago actual ' . $commitmentDate], 422);

        $data = [
            'oldEndDate' => $commitmentDate,
            'newEndDate' => $request->newEndDate,
            'reason' => $request->reason,
            'commitment_id' => $commitment->id,
        ];

        $extension = Extension::create($data);
        $commitment->update(['payment_date' => $extension->newEndDate]);
        $extension = Extension::find($extension->id);
        return response()->json($extension);
    }

    public function show(int $id)
    {
        $extension = Extension::find($id);
        if (!$extension) return response()->json(['error' => 'Extension not found'], 404);
        return response()->json($extension);
    }

    public function update(Request $request, int $id)
    {

    }

    /**
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/extension/{id}",
     *     tags={"Extension"},
     *     summary="Delete an extension",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="ID of the extension", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Delete an extension", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(@OA\Property(property="error", type="string", example="Extension not found"))),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $extension = Extension::find($id);
        $commitment = Commitment::find($extension->commitment_id);
        $extension->delete();
        $commitment->update(['payment_date' => $extension->oldEndDate]);
        return response()->json(['message' => 'Extension deleted']);
    }
}
