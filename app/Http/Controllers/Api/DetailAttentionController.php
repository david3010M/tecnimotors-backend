<?php

namespace App\Http\Controllers\Api;

use App\Exports\Attention\DetailAttentionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DetailAttention\IndexDetailAttentionRequest;
use App\Http\Resources\DetailAttentionResource;
use App\Models\Attention;
use App\Models\DetailAttention;
use App\Utils\UtilFunctions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DetailAttentionController extends Controller
{


    public function index(IndexDetailAttentionRequest $request)
    {
        $query = DetailAttention::query()
            ->with(['attention.vehicle', 'attention.vehicle.person']) // relaciones necesarias
            ->when($request->filled('plate'), function ($q) use ($request) {
                $q->whereHas('attention.vehicle', function ($subQuery) use ($request) {
                    $subQuery->whereRaw('UPPER(plate) LIKE ?', ['%' . strtoupper($request->input('plate')) . '%']);
                });
            })
            ->when($request->filled('client'), function ($q) use ($request) {
                $search = strtoupper($request->input('client'));
                $q->whereHas('attention.vehicle.person', function ($subQuery) use ($search) {
                    $subQuery->whereRaw("
                    UPPER(CONCAT_WS(' ', names, fatherSurname, motherSurname, businessName)) LIKE ?
                ", ["%$search%"]);
                });
            })
            ->when($request->filled('correlativo'), function ($q) use ($request) {
                $correlativo = strtoupper($request->input('correlativo'));
                $q->whereHas('attention', function ($subQuery) use ($correlativo) {
                    $subQuery->whereRaw('UPPER(correlativo) LIKE ?', ["%$correlativo%"]);
                });
            });

        return $this->getFilteredResults(
            $query,
            $request,
            DetailAttention::filters,
            DetailAttention::sorts,
            DetailAttentionResource::class
        );
    }

    private function getServiciosData(IndexDetailAttentionRequest $request)
    {
        $request['all'] = true;
        $request['type'] = "Service";

        $resourceCollection = $this->index($request);
        return $resourceCollection->toArray($request);
    }

    public function report(IndexDetailAttentionRequest $request)
    {
        $dataArray = $this->getServiciosData($request);

        $pdf = Pdf::loadView('details-semaforo', [
            'details' => $dataArray,
        ]);

        return $pdf->download('details-semaforo.pdf');
    }
    public function exportExcel(IndexDetailAttentionRequest $request)
    {
        // Forzamos los parámetros necesarios
        $clonedRequest = clone $request;
        $clonedRequest->offsetSet('all', true);
        $clonedRequest->offsetSet('type', 'Service');

        // Obtenemos los datos
        $dataArray = $this->getServiciosData($clonedRequest);

        // 🔍 Filtramos para asegurarnos que solo queden los de tipo 'Service'
        $filteredData = array_filter($dataArray, function ($item) {
            return isset($item['type']) && $item['type'] === 'Service';
        });

        // Generamos el Excel
        $bytes = UtilFunctions::generateReportServiceExcel($filteredData, auth()->user(), '-');

        return response($bytes)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="reporte_servicios.xlsx"');
    }


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
        $user = Auth::user();
        $typeofUser_id = $user->typeofUser_id;

        $query = DetailAttention::with(['worker.person'])->where('type', 'Service')
            ->with('service');

        if ($typeofUser_id != 1 && $typeofUser_id != 2) {
            $query->where('worker_id', $id);
        }

        $detailAttention = $query->paginate(15);

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

    /**
     * Update the specified detailAttention in storage.
     * @OA\Put (
     *      path="/tecnimotors-backend/public/api/detailAttention/{id}",
     *      tags={"DetailAttention"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="DetailAttention ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DetailAttentionRequestUpdate")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="DetailAttention updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/DetailAttentionNoRelations")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="DetailAttention not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="DetailAttention not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */

    public function update(Request $request, int $id)
    {
        $detail = DetailAttention::find($id);

        if ($detail === null) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'quantity' => 'required|numeric',
            'salePrice' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'quantity' => $request->input('quantity'),
            'saleprice' => $request->input('salePrice'),
        ];

        $detail->update($data);

        $object = Attention::find($detail->attention_id);

        $object->totalProducts = $object->details()->where('type', 'Product')->get()->sum(function ($detail) {
            return $detail->saleprice * $detail->quantity;
        });


        $object->totalService = $object->details()->where('type', 'Service')->get()->sum(function ($detail) {
            return $detail->saleprice * $detail->quantity;
        });

        $object->total = $object->details()->get()->sum(function ($detail) {
            return $detail->saleprice * $detail->quantity;
        });
        $object->save();

        $detail = DetailAttention::find($detail->id);

        return response()->json($detail);
    }

}
