<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetSheetDetailsResource;
use App\Http\Resources\BudgetSheetResource;
use App\Models\Attention;
use App\Models\budgetSheet;
use App\Models\Commitment;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BudgetSheetController extends Controller
{
    /**
     * Get all BudgetSheets with optional filters
     *
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/budgetSheet",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="attention_vehicle_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Attention Vehicle ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by Status",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of BudgetSheets with optional filters applied",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BudgetSheet")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/budgetSheet?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/budgetSheet?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/budgetSheet"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Obtenemos los filtros de la solicitud
        $attentionVehicleId = $request->query('attention_vehicle_id');
        $status = $request->query('status');

        // Consulta base, incluyendo las relaciones hasta `person`
        $query = BudgetSheet::with(['attention', 'attention.concession']);

        // Aplicamos filtros si se proporcionan
        if ($attentionVehicleId) {
            $query->whereHas('attention', function ($q) use ($attentionVehicleId) {
                $q->where('vehicle_id', $attentionVehicleId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Obtenemos la paginación completa con los filtros aplicados
        $budgetSheets = $query->orderBy('id', 'desc')->paginate(15);

        // Devolvemos los datos paginados como respuesta JSON
        return response()->json([
            'total' => $budgetSheets->total(),
            'data' => $budgetSheets->map(function ($item) {
                $data = $item->toArray(); // Convertimos el modelo en un array
                $data['person_id'] = $item->attention->vehicle->person_id ?? null; // Agregamos el campo person_id
                $data['number'] = 'PRES' . substr($item->attention->number, 4);

                return $data;
            })->toArray(),
            'current_page' => $budgetSheets->currentPage(),
            'last_page' => $budgetSheets->lastPage(),
            'per_page' => $budgetSheets->perPage(),
            'first_page_url' => $budgetSheets->url(1),
            'from' => $budgetSheets->firstItem(),
            'next_page_url' => $budgetSheets->nextPageUrl(),
            'path' => $budgetSheets->path(),
            'prev_page_url' => $budgetSheets->previousPageUrl(),
            'to' => $budgetSheets->lastItem(),
        ]);
    }


    /**
     * Get a single BudgetSheet
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/budgetSheet/{id}",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="BudgetSheet ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BudgetSheet data",
     *         @OA\JsonContent(ref="#/components/schemas/BudgetSheet")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="BudgetSheet not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BudgetSheet not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $budgetSheet = budgetSheet::with([
            'attention',
            'attention.details',
            'attention.details.product.unit',
            'attention.vehicle.person',
            'attention.vehicle.vehicleModel.brand',
            'attention.elements',
            'attention.routeImages',
            'attention.worker.person',
        ])->find($id);

        if ($budgetSheet && $budgetSheet->attention && $budgetSheet->attention->details) {
            $budgetSheet->attention->details = $budgetSheet->attention->details->map(function ($detail) {
                if ($detail->product) {
                    $detail->product->unitValue = round($detail->product->sale_price / 1.18, 2);
                }
                if ($detail->service) {
                    $detail->service->unitValue = round($detail->service->saleprice / 1.18, 2);
                }
                return $detail;
            });
        }

        if (!$budgetSheet) {
            return response()->json(['message' => 'BudgetSheet not found'], 404);
        }

        return response()->json($budgetSheet);
    }

    /**
     * Create a new BudgetSheet
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/budgetSheet",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"attention_id"},
     *              @OA\Property(property="attention_id", type="integer", example=1),
     *              @OA\Property(property="paymentType", type="string", enum={"Contado", "Credito"}, example="Contado"),
     *              @OA\Property(property="percentageDiscount", type="decimal", example=0),
     *              @OA\Property(property="commitments", type="array", @OA\Items(
     *                  @OA\Property(property="price", type="decimal", example=100),
     *                  @OA\Property(property="paymentDate", type="integer", example=30)
     *              )),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BudgetSheet created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BudgetSheet")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'attention_id' => [
                'required',
                'exists:attentions,id',
                Rule::unique('budget_sheets')->where(function ($query) use ($request) {
                    return $query->where('attention_id', $request->input('attention_id'));
                }),
            ],
            'percentageDiscount' => 'required|numeric|between:0,100',
            'paymentType' => 'nullable|string|in:Contado,Credito',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $attention = Attention::find($request->input('attention_id'));

        $tipo = 'PRES';
        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM budget_sheets a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int)$resultado;

        $subtotal = floatval($attention->total) / 1.18;
        $igv = $subtotal * 0.18;

        $data = [
            'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentType' => $request->input('paymentType'),
            'totalService' => $attention->totalService ?? 0.0,
            'totalProducts' => $attention->totalProducts ?? 0.0,
            'debtAmount' => $attention->debtAmount,
            'total' => $attention->total,
            'discount' => 0,
            'subtotal' => $subtotal ?? 0.0,
            'igv' => $igv ?? 0.0,
            'attention_id' => $request->input('attention_id'),
        ];

        $object = budgetSheet::create($data);

        $object = budgetSheet::with(['attention'])->find($object->id);
        return response()->json($object);
    }

    /**
     * Update a budgetSheet
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/budgetSheet/{id}",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the budgetSheet",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"attention_id"},
     *              @OA\Property(property="paymentType", type="string", example="Al Contado"),
     *              @OA\Property(property="percentageDiscount", type="decimal", example=0),
     *
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="attention updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TypeAttention")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="attention not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="attention not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {

        $object = budgetSheet::find($id);
        if (!$object) {
            return response()->json(['message' => 'Budget Sheet not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'attention_id' => 'nullable|exists:attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $attention = Attention::find($object->attention_id);

        $percentageDiscount = floatval($request->input('percentageDiscount', 0)) / 100;
        $subtotal = floatval($attention->total);
        $discount = $subtotal * $percentageDiscount;

        $igv = ($subtotal - $discount) * 0.18;
        $total = ($subtotal - $discount) * 1.18;

        $data = [
            'paymentType' => $request->input('paymentType'),
            'totalService' => $attention->totalService ?? 0.0,
            'totalProducts' => $attention->totalProducts ?? 0.0,
            'debtAmount' => $attention->debtAmount,
            'total' => $total ?? 0.0,
            'discount' => $discount ?? 0.0,
            'subtotal' => $subtotal ?? 0.0,
            'igv' => $igv ?? 0.0,

        ];

        $object->update($data);

        $object = budgetSheet::with(['attention'])->find($object->id);

        return response()->json($object);
    }

    /**
     * Delete an BudgetSheet
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/budgetSheet/{id}",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="BudgetSheet ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BudgetSheet deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BudgetSheet deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="BudgetSheet not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BudgetSheet not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="BudgetSheet has budgetSheets for attention",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BudgetSheet has budgetSheets for attention")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $budgetSheet = budgetSheet::find($id);
        if (!$budgetSheet) {
            return response()->json(['message' => 'BudgetSheet not found'], 404);
        }
        
        $budgetSheet->delete();

        return response()->json(['message' => 'BudgetSheet deleted']);
    }

    /**
     * Update the status of a budgetSheet to "Pagado sin boletear"
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/budgetSheet/{id}/updateStatusSinBoletear",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="BudgetSheet ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response( response=200, description="BudgetSheet updated successfully", @OA\JsonContent(ref="#/components/schemas/BudgetSheet")),
     *     @OA\Response( response=404, description="BudgetSheet not found", @OA\JsonContent( @OA\Property(property="message", type="string", example="BudgetSheet not found"))),
     *     @OA\Response( response=401, description="Unauthorized", @OA\JsonContent( @OA\Property( property="message", type="string", example="Unauthenticated")))
     * )
     */
    public function updateStatusSinBoletear(int $id)
    {
        $budgetSheet = budgetSheet::find($id);
        if (!$budgetSheet) {
            return response()->json(['message' => 'BudgetSheet not found'], 404);
        }

        $budgetSheet->status = 'Pagado sin boletear';
        $budgetSheet->save();

        return response()->json($budgetSheet);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/budgetSheet/findBudgetSheetByPersonId",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="search", in="query", required=true, description="Search term", @OA\Schema(type="string")),
     *     @OA\Response( response=200, description="BudgetSheet updated successfully", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BudgetSheet"))),
     *     @OA\Response( response=401, description="Unauthorized", @OA\JsonContent( @OA\Property( property="message", type="string", example="Unauthenticated")))
     * )
     */
    public function findBudgetSheetByPersonId(Request $request)
    {
        $search = $request->query('search');

        $budgetSheets = BudgetSheet::where('status', '!=', 'Pagado sin boletear')
            ->where('status', '!=', Constants::BUDGET_SHEET_FACTURADO)
            ->where(function ($query) use ($search) {
                $query->whereHas('attention.vehicle.person', function ($query) use ($search) {
                    $query->where('names', 'like', '%' . $search . '%')
                        ->orWhere('fatherSurname', 'like', '%' . $search . '%')
                        ->orWhere('motherSurname', 'like', '%' . $search . '%')
                        ->orWhere('businessName', 'like', '%' . $search . '%');
                })
                    ->orWhere('number', 'like', '%' . $search . '%');
            })
            ->limit(30)
            ->get();

        return response()->json(BudgetSheetResource::collection($budgetSheets));
    }

}
