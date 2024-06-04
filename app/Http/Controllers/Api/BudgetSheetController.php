<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\budgetSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetSheetController extends Controller
{
    /**
     * Get all BudgetSheets
     *
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/budgetSheet",
     *     tags={"BudgetSheet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active BudgetSheets",
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

    public function index()
    {
        return response()->json(budgetSheet::with(['attention'])->simplePaginate(15));
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
        $budgetSheet = budgetSheet::with(['attention'])->find($id);
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
     * @OA\Property(property="paymentType", type="string", example="Al Contado"),
     * @OA\Property(property="discount", type="decimal", example=0),
     * @OA\Property(property="subtotal", type="decimal", example=0),
     * @OA\Property(property="igv", type="decimal", example=0),
     *
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
            'attention_id' => 'required|exists:attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $attention = Attention::find($request->input('attention_id'));

        $tipo = 'PRES';
        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM budget_sheets a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int)$resultado;

        $data = [
            'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentType' => $request->input('paymentType'),
            'totalService' => $attention->totalService ?? 0.0,
            'totalProducts' => $attention->totalProducts ?? 0.0,
            'debtAmount' => $attention->debtAmount,
            'total' => $attention->total ?? 0.0,
            'discount' => $request->input('discount') ?? 0.0,
            'subtotal' => $request->input('subtotal') ?? 0.0,
            'igv' => $request->input('igv') ?? 0.0,
            'attention_id' => $request->input('attention_id'),
        ];

        $object = budgetSheet::create($data);
        $object = budgetSheet::with(['attention'])->find($object->id);
        return response()->json($object);
    }

    /**
     * Update a budgedSheet
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/budgedSheet/{id}",
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
     *              @OA\Property(property="attention_id", type="integer", example=1),
     * @OA\Property(property="paymentType", type="string", example="Al Contado"),
     * @OA\Property(property="discount", type="decimal", example=0),
     * @OA\Property(property="subtotal", type="decimal", example=0),
     * @OA\Property(property="igv", type="decimal", example=0),
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
            'attention_id' => 'required|exists:attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $attention = Attention::find($request->input('attention_id'));

        $data = [
            'paymentType' => $request->input('paymentType'),
            'totalService' => $attention->totalService ?? 0.0,
            'totalProducts' => $attention->totalProducts ?? 0.0,
            'debtAmount' => $attention->debtAmount,
            'total' => $attention->total ?? 0.0,
            'discount' => $request->input('discount') ?? 0.0,
            'subtotal' => $request->input('subtotal') ?? 0.0,
            'igv' => $request->input('igv') ?? 0.0,
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

        //Verificar relaciones con amoritzaciones

        $budgetSheet->delete();

        return response()->json(['message' => 'BudgetSheet deleted']);
    }

}
