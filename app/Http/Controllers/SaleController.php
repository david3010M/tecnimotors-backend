<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/sale",
     *     tags={"Sales"},
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
    public function index(IndexSaleRequest $request)
    {
        return $this->getFilteredResults(
            Sale::class,
            $request,
            Sale::filters,
            Sale::sorts,
            SaleResource::class
        );
    }

    public function store(StoreSaleRequest $request)
    {
        $data = [
            'number' => $this->nextCorrelative(Sale::class, 'number'),
            'paymentDate' => $request->paymentDate,
            'documentType' => $request->documentType,
            'saleType' => $request->saleType,
            'detractionCode' => $request->detractionCode ?? null,
            'detractionPercentage' => $request->detractionPercentage ?? null,
            'paymentType' => $request->paymentType,
            'status' => 'PENDIENTE',
            'person_id' => $request->person_id,
            'budget_sheet_id' => $request->budget_sheet_id,
        ];

        $sale = Sale::create($data);
        $sale = Sale::find($sale->id);

        return response()->json(SaleResource::make($sale));

    }

    public function show(Sale $sale)
    {
        $sale = Sale::with(
            [
                'person',
                'budgetSheet.attention',
                'budgetSheet.attention.worker.person',
                'budgetSheet.attention.vehicle.person',
                'budgetSheet.attention.vehicle.vehicleModel.brand',
                'budgetSheet.attention.details',
                'budgetSheet.attention.details.product.unit',
                'budgetSheet.attention.routeImages',
                'budgetSheet.attention.elements',
            ]
        )->find($sale->id);
        return response()->json($sale);
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        //
    }

    public function destroy(Sale $sale)
    {
        //
    }
}
