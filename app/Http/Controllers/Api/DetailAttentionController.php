<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\DetailAttention;
use Illuminate\Http\Request;

class DetailAttentionController extends Controller
{
    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/detailAttention/{id}",
     *     tags={"DetailAttention"},
     *     security={{"bearerAuth": {}}},
     *     summary="List all Detail Attention",
     *     description="List all Detail Attention",
     *     operationId="detailAttention",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Detail Attention",
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
     *             @OA\Property(property="detailAttention", ref="#/components/schemas/DetailAttentionNoRelations"),
     *             @OA\Property(property="observation", type="string", example="Observation"),
     *             @OA\Property(property="product", type="array", @OA\Items(ref="#/components/schemas/ProductNoRelations"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Detail Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Detail Attention not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $detailAttention = DetailAttention::where('type', 'Service')->find($id);

        if (!$detailAttention) {
            return response()->json(['message' => 'Detail Attention not found'], 404);
        }

        $attention = Attention::find($detailAttention->attention_id);
        $products = $attention->getDetailsProducts();

        return response()->json(
            [
                'detailAttention' => $detailAttention,
                'observation' => $attention->observations,
                'product' => $products,
            ]
        );
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/detailAttentionByWorker/{id}",
     *     tags={"DetailAttention"},
     *     security={{"bearerAuth": {}}},
     *     summary="List all Detail Attention by Worker",
     *     description="List all Detail Attention by Worker",
     *     operationId="detailAttentionByWorker",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of worker",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DetailAttentionServicePaginate")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Detail Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Detail Attention not found")
     *         )
     *     ),
     * @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function detailAttentionByWorker(int $id)
    {
        $detailAttention = DetailAttention::where('worker_id', $id)
            ->where('type', 'Service')
            ->with('service')->simplePaginate(15);

        if (!$detailAttention) {
            return response()->json(['message' => 'Detail Attention not found'], 404);
        }

        return response()->json($detailAttention);
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/detailAttentionStart/{id}",
     *     tags={"DetailAttention"},
     *     security={{"bearerAuth": {}}},
     *     summary="Start Detail Attention",
     *     description="Start Detail Attention",
     *     operationId="start",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Detail Attention",
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
     *              @OA\Property(property="detailAttention", ref="#/components/schemas/DetailAttentionNoRelations"),
     *              @OA\Property(property="observation", type="string", example="Observation"),
     *              @OA\Property(property="product", type="array", @OA\Items(ref="#/components/schemas/ProductNoRelations"))
     *          )
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Detail Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Detail Attention not found")
     *         )
     *     ),
     * @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function start(int $id)
    {
        $detailAttention = DetailAttention::find($id);

        if (!$detailAttention) {
            return response()->json(['message' => 'Detail Attention not found'], 404);
        }

        $detailAttention->status = 'Inicio';
        $detailAttention->dateCurrent = date('Y-m-d H:i:s');
        $detailAttention->save();

        $attention = Attention::find($detailAttention->attention_id);
        $products = $attention->getDetailsProducts();

        return response()->json(
            [
                'detailAttention' => $detailAttention,
                'observation' => $attention->observations,
                'product' => $products,
            ]
        );
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/detailAttentionFinish/{id}",
     *     tags={"DetailAttention"},
     *     security={{"bearerAuth": {}}},
     *     summary="Finish Detail Attention",
     *     description="Finish Detail Attention",
     *     operationId="finish",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Detail Attention",
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
     *              @OA\Property(property="detailAttention", ref="#/components/schemas/DetailAttentionNoRelations"),
     *              @OA\Property(property="observation", type="string", example="Observation"),
     *              @OA\Property(property="product", type="array", @OA\Items(ref="#/components/schemas/ProductNoRelations"))
     *          )
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Detail Attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Detail Attention not found")
     *         )
     *     ),
     * @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function finish(int $id)
    {
        $detailAttention = DetailAttention::find($id);

        if (!$detailAttention) {
            return response()->json(['message' => 'Detail Attention not found'], 404);
        }

        $detailAttention->status = 'Fin';
        $detailAttention->dateMax = date('Y-m-d H:i:s');
        $detailAttention->save();

        $attention = Attention::find($detailAttention->attention_id);
        $products = $attention->getDetailsProducts();

        return response()->json(
            [
                'detailAttention' => $detailAttention,
                'observation' => $attention->observations,
                'product' => $products,
            ]
        );
    }

}
