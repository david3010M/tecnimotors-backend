<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestSale;
use App\Http\Resources\SaleResource;
use App\Models\budgetSheet;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Utils\Constants;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/sale",
     *     tags={"Sale"},
     *     summary="Get all sales",
     *     description="Get all sales",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="number", in="query", description="Filter by number", @OA\Schema(type="string")),
     *     @OA\Parameter( name="paymentDate", in="query", description="Filter by paymentDate", @OA\Schema(type="array", @OA\Items(type="string", format="date"))),
     *     @OA\Parameter( name="documentType", in="query", description="Filter by documentType", @OA\Schema(type="string", enum={"BOLETA", "FACTURA"})),
     *     @OA\Parameter( name="saleType", in="query", description="Filter by saleType", @OA\Schema(type="string", enum={"NORMAL", "DETRACCION"})),
     *     @OA\Parameter( name="detractionCode", in="query", description="Filter by detractionCode", @OA\Schema(type="string")),
     *     @OA\Parameter( name="detractionPercentage", in="query", description="Filter by detractionPercentage", @OA\Schema(type="string")),
     *     @OA\Parameter( name="paymentType", in="query", description="Filter by paymentType", @OA\Schema(type="string", enum={"CONTADO", "CREDITO"})),
     *     @OA\Parameter( name="status", in="query", description="Filter by status", @OA\Schema(type="string")),
     *     @OA\Parameter( name="person_id", in="query", description="Filter by person_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="person$documentNumber", in="query", description="Filter by person$documentNumber", @OA\Schema(type="string")),
     *     @OA\Parameter( name="budget_sheet_id", in="query", description="Filter by budget_sheet_id", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/SaleCollection")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function index(IndexRequestSale $request)
    {
        return $this->getFilteredResults(
            Sale::class,
            $request,
            Sale::filters,
            Sale::sorts,
            SaleResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/sale",
     *     tags={"Sale"},
     *     summary="Create a sale",
     *     description="Create a sale",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/StoreSaleRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/SaleSingleResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function store(StoreSaleRequest $request)
    {
        $budgetSheet = budgetSheet::find($request->budget_sheet_id);
        if (!$budgetSheet) return response()->json(['message' => 'Budget sheet not found'], 404);

        $data = [
            'number' => $this->nextCorrelative(Sale::class, 'number'),
            'paymentDate' => $request->paymentDate,
            'documentType' => $request->documentType,
            'saleType' => $request->saleType,
            'detractionCode' => $request->saleType === Constants::SALE_DETRACCION ? $request->detractionCode : null,
            'detractionPercentage' => $request->saleType === Constants::SALE_DETRACCION ? $request->detractionPercentage : null,
            'paymentType' => $request->paymentType,
            'status' => Constants::SALE_PENDIENTE,
            'total' => $budgetSheet->total,
            'person_id' => $request->person_id,
            'budget_sheet_id' => $request->budget_sheet_id,
        ];

        $sale = Sale::create($data);
        $sale = Sale::find($sale->id);

        $budgetSheet->status = Constants::BUDGET_SHEET_FACTURADO;
        $budgetSheet->save();
        return response()->json(SaleResource::make($sale)->withBudgetSheet());
    }

    /**
     * Display the specified resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Get a sale",
     *     description="Get a sale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Sale ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/SaleSingleResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="404", description="Not Found")
     * )
     */
    public function show(int $id)
    {
        $sale = Sale::with(
            [
                'person',
                'budgetSheet.commitments',
                'budgetSheet.attention',
                'budgetSheet.attention.worker.person',
                'budgetSheet.attention.vehicle.person',
                'budgetSheet.attention.vehicle.vehicleModel.brand',
                'budgetSheet.attention.details',
                'budgetSheet.attention.details.product.unit',
                'budgetSheet.attention.routeImages',
                'budgetSheet.attention.elements',
            ]
        )->find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);
        return response()->json(SaleResource::make($sale)->withBudgetSheet());
    }

    /**
     * Update the specified resource in storage.
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Update a sale",
     *     description="Update a sale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Sale ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/UpdateSaleRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/SaleSingleResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="404", description="Not Found"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function update(UpdateSaleRequest $request, int $id)
    {
        $sale = Sale::find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);
        if ($sale->status === Constants::SALE_FACTURADO) return response()->json(['message' => 'Sale already invoiced'], 422);

        $budgetSheet = budgetSheet::find($request->budget_sheet_id);
        if (!$budgetSheet) return response()->json(['message' => 'Budget sheet not found'], 404);

        $data = [
            'paymentDate' => $request->paymentDate ?? $sale->paymentDate,
            'documentType' => $request->documentType ?? $sale->documentType,
            'saleType' => $request->saleType ?? $sale->saleType,
            'detractionCode' => ($request->saleType === Constants::SALE_DETRACCION ? $request->detractionCode : null) ?? $sale->detractionCode,
            'detractionPercentage' => ($request->saleType === Constants::SALE_DETRACCION ? $request->detractionPercentage : null) ?? $sale->detractionPercentage,
            'paymentType' => $request->paymentType ?? $sale->paymentType,
            'total' => $budgetSheet->total ?? $sale->total,
            'person_id' => $request->person_id ?? $sale->person_id,
            'budget_sheet_id' => $request->budget_sheet_id ?? $sale->budget_sheet_id,
        ];

        $sale->update($data);
        $sale = Sale::find($sale->id);

        $budgetSheet->status = Constants::BUDGET_SHEET_FACTURADO;
        $budgetSheet->save();

        return response()->json(SaleResource::make($sale));
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Delete a sale",
     *     description="Delete a sale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Sale ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(@OA\Property(property="message", type="string", example="Sale deleted"))),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="404", description="Not Found")
     * )
     */
    public function destroy(int $id)
    {
        $sale = Sale::find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);
        if ($sale->status === Constants::SALE_FACTURADO) return response()->json(['message' => 'Sale already invoiced'], 422);
        $sale->delete();
        return response()->json(['message' => 'Sale deleted']);
    }
}
