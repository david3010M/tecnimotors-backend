<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestSale;
use App\Http\Resources\SaleResource;
use App\Models\Amortization;
use App\Models\budgetSheet;
use App\Models\Commitment;
use App\Models\Moviment;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\SaleDetail;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/StoreSaleRequest")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/SaleSingleResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function store(StoreSaleRequest $request)
    {
        $budgetSheet = budgetSheet::find($request->budget_sheet_id);

        $subtotal = 0;
        foreach ($request->saleDetails as $saleDetail) {
            $subtotal += $saleDetail['subTotal'];
        }
        $igv = $subtotal * Constants::IGV;
        $total = $subtotal + $igv;

        $data = [
            'number' => $this->nextCorrelativeQuery(Sale::where('documentType', $request->documentType), 'number'),
            'paymentDate' => $request->input('paymentDate'),
            'documentType' => $request->input('documentType'),
            'saleType' => $request->input('saleType'),
            'detractionCode' => $request->input('saleType') === Constants::SALE_DETRACCION ? $request->input('detractionCode') : '',
            'detractionPercentage' => $request->input('saleType') === Constants::SALE_DETRACCION ? $request->input('detractionPercentage') : '',
            'paymentType' => $request->input('paymentType'),
            'status' => Constants::SALE_PENDIENTE,
            'total' => $total,
            'person_id' => $request->input('person_id'),
            'budget_sheet_id' => $request->input('budget_sheet_id'),
            'cash_id' => 1,
        ];

        $sale = Sale::make($data);

        if ($sale->paymentType == Constants::SALE_CONTADO) {
//            CHECK IF CASH MOVEMENT EXISTS
            $movCaja = Moviment::where('status', 'Activa')->where('paymentConcept_id', 1)->first();
            if (!$movCaja) {
                if ($request->input('paymentConcept_id') != 1) {
                    return response()->json([
                        "message" => "Debe Aperturar Caja",
                    ], 422);
                }
            } else {
                if ($request->input('paymentConcept_id') == 1) {
                    return response()->json([
                        "message" => "Caja Ya Aperturada",
                    ], 422);
                }
            }

            $routeVoucher = null;
            $numberVoucher = null;
            $bank_id = null;
            $depositAmount = 0;
            //        IF IS BANK PAYMENT
            if ($request->input('isBankPayment') == 1) {
                $routeVoucher = 'ruta.jpg';
                $numberVoucher = $request->input('numberVoucher');
                $bank_id = $request->input('bank_id');
                $depositAmount = $request->input('deposit') ?? 0;
            }

//        PAYMENT METHODS
            $efectivo = $request->input('effective') ?? 0;
            $yape = $request->input('yape') ?? 0;
            $plin = $request->input('plin') ?? 0;
            $tarjeta = $request->input('card') ?? 0;
            $deposito = $depositAmount ?? 0;

//        TOTAL
            $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

            if ($total == 0) {
                return response()->json([
                    "error" => "El monto a pagar no puede ser 0",
                ], 422);
            }

            if (round($sale->total - $total, 2) != 0) {
                return response()->json([
                    "error" => "El monto a pagar no coincide con el total " . number_format($sale->total, 2) .
                        " diferencia " . number_format($sale->total - $total, 2),
                ], 422);
            }

//            THEN SAVE SALE
            $sale->save();
            $commitment = Commitment::create([
                'numberQuota' => 1,
                'price' => $sale->total,
                'balance' => $sale->total,
                'status' => Constants::COMMITMENT_PAGADO,
                'payment_type' => Constants::COMMITMENT_CONTADO,
                'payment_date' => today(),
                'sale_id' => $sale->id,
            ]);

//            MOVEMENT CREATION
            $tipo = 'M001';
            $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);
            $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
            $siguienteNum = (int)$resultado;

//        DATA
            $routeVoucher = null;
            $numberVoucher = null;
            $bank_id = null;
            $depositAmount = 0;

            $movement = Moviment::create([
                'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
                'paymentDate' => now(),
                'total' => $commitment->price,
                'yape' => $request->input('yape') ?? 0,
                'deposit' => $depositAmount ?? 0,
                'nro_operation' => $request->input('nro_operation'),
                'cash' => $request->input('cash') ?? 0,
                'card' => $request->input('card') ?? 0,
                'plin' => $request->input('plin') ?? 0,
                'isBankPayment' => $request->input('isBankPayment'),
                'routeVoucher' => $routeVoucher,
                'numberVoucher' => $numberVoucher,
                'typeDocument' => 'Ingreso',
                'bank_id' => $request->input('bank_id'),
                'comment' => $request->input('comment') ?? '-',
                'status' => 'Generada',
                'paymentConcept_id' => 7,
                'person_id' => $request->input('person_id'),
                'user_id' => auth()->id(),
                'sale_id' => $sale->id,
            ]);

            $image = $request->file('routeVoucher');

            //        IF IMAGE
            if ($image) {
                Log::info('Imagen recibida: ' . $image->getClientOriginalName());
                $file = $image;
                $currentTime = now();
                $filename = $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/photosVouchers', $filename);
                Log::info('Imagen almacenada en: ' . $path);
                $rutaImagen = Storage::url($path);
                $movement->routeVoucher = $rutaImagen;
                $movement->save();
                Log::info('Imagen guardada en la base de datos con ruta: ' . $rutaImagen);
            }

            $sale->update([
                'yape' => $yape,
                'deposit' => $depositAmount,
                'nro_operation' => $request->input('nro_operation'),
                'effective' => $efectivo,
                'card' => $tarjeta,
                'plin' => $plin,
                'isBankPayment' => $request->input('isBankPayment'),
                'bank_id' => $request->input('bank_id'),
                'numberVoucher' => $numberVoucher,
                'routeVoucher' => $routeVoucher,
                'comment' => $request->input('comment'),
            ]);

//            AMORTIZATION CREATION
            $tipo = 'AMRT';
            $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);
            $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM amortizations WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
            $siguienteNum = (int)$resultado;

            Amortization::create([
                'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
                'amount' => $commitment->price,
                'status' => Constants::AMORTIZATION_PAID,
                'paymentDate' => now(),
                'moviment_id' => $movement->id,
                'commitment_id' => $commitment->id,
            ]);

        } else if ($sale->paymentType == Constants::SALE_CREDITO) {
            $sumCommitments = array_sum(array_column($request->input('commitments'), 'price'));
            if (round($sumCommitments, 4) != round($sale->total, 4)) {
                return response()->json(['error' => 'La suma de los compromisos no coincide con el total ' . $sale->total . ' diferencia ' . ($sale->total - $sumCommitments)], 422);
            }
            $sale->save();
            $commitments = $request->input('commitments');
            foreach ($commitments as $index => $commitment) {
                Commitment::create([
                    'numberQuota' => $index + 1,
                    'price' => $commitment['price'],
                    'balance' => $commitment['price'],
                    'amount' => 0,
                    'status' => Constants::COMMITMENT_PENDING,
                    'payment_date' => Carbon::today()->addDays($commitment['paymentDate']),
                    'payment_type' => Constants::COMMITMENT_CREDITO,
                    'sale_id' => $sale->id,
                ]);
            }
        }

        $taxableOperation = 0;

        foreach ($request->saleDetails as $saleDetail) {
            SaleDetail::create([
                'description' => $saleDetail['description'],
                'unit' => $saleDetail['unit'],
                'quantity' => $saleDetail['quantity'],
                'unitValue' => $saleDetail['unitValue'],
                'unitPrice' => $saleDetail['unitPrice'],
                'discount' => $saleDetail['discount'] ?? 0,
                'subTotal' => $saleDetail['subTotal'],
                'sale_id' => $sale->id,
            ]);
            $taxableOperation += $saleDetail['subTotal'];
        }

        $igv = $taxableOperation * Constants::IGV;
        $total = $taxableOperation + $igv;

        $sale->update([
            'taxableOperation' => $taxableOperation,
            'igv' => $igv,
            'total' => $total,
        ]);

        $sale = Sale::find($sale->id);
        if ($budgetSheet) {
            $budgetSheet->status = Constants::BUDGET_SHEET_FACTURADO;
            $budgetSheet->save();
        }
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
                'saleDetails',
                'person',
                'cash',
                'commitments',
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
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        $budgetSheet = BudgetSheet::find($request->budget_sheet_id);
        if (!$budgetSheet) return response()->json(['message' => 'Budget sheet not found'], 404);

        $subtotal = 0;
        foreach ($request->saleDetails as $saleDetail) {
            $subtotal += $saleDetail['subTotal'];
        }
        $igv = $subtotal * Constants::IGV;
        $total = $subtotal + $igv;

        $sale->update([
            'paymentDate' => $request->input('paymentDate'),
            'documentType' => $request->input('documentType'),
            'saleType' => $request->input('saleType'),
            'detractionCode' => $request->input('saleType') === Constants::SALE_DETRACCION ? $request->input('detractionCode') : '',
            'detractionPercentage' => $request->input('saleType') === Constants::SALE_DETRACCION ? $request->input('detractionPercentage') : '',
            'paymentType' => $request->input('paymentType'),
            'status' => Constants::SALE_PENDIENTE,
            'total' => $total,
            'person_id' => $request->input('person_id'),
            'budget_sheet_id' => $request->input('budget_sheet_id'),
            'cash_id' => 1,
        ]);

        if ($sale->paymentType == Constants::SALE_CONTADO) {
            $movCaja = Moviment::where('status', 'Activa')->where('paymentConcept_id', 1)->first();
            if (!$movCaja) {
                if ($request->input('paymentConcept_id') != 1) {
                    return response()->json(["message" => "Debe Aperturar Caja"], 422);
                }
            } else {
                if ($request->input('paymentConcept_id') == 1) {
                    return response()->json(["message" => "Caja Ya Aperturada"], 422);
                }
            }

            $routeVoucher = null;
            $numberVoucher = null;
            $bank_id = null;
            $depositAmount = 0;

            if ($request->input('isBankPayment') == 1) {
                $routeVoucher = 'ruta.jpg';
                $numberVoucher = $request->input('numberVoucher');
                $bank_id = $request->input('bank_id');
                $depositAmount = $request->input('deposit') ?? 0;
            }

            $efectivo = $request->input('effective') ?? 0;
            $yape = $request->input('yape') ?? 0;
            $plin = $request->input('plin') ?? 0;
            $tarjeta = $request->input('card') ?? 0;
            $deposito = $depositAmount ?? 0;

            $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

            if ($total == 0) {
                return response()->json(["error" => "El monto a pagar no puede ser 0"], 422);
            }

            if (round($sale->total - $total, 2) != 0) {
                return response()->json([
                    "error" => "El monto a pagar no coincide con el total " . number_format($sale->total, 2) .
                        " diferencia " . number_format($sale->total - $total, 2),
                ], 422);
            }

            $commitment = $sale->commitment()->update([
                'price' => $sale->total,
                'balance' => $sale->total,
                'status' => Constants::COMMITMENT_PAGADO,
                'payment_type' => Constants::COMMITMENT_CONTADO,
                'payment_date' => now(),
            ]);

            $sale->moviment()->update([
                'total' => $commitment->price,
                'yape' => $request->input('yape') ?? 0,
                'deposit' => $depositAmount ?? 0,
                'nro_operation' => $request->input('nro_operation'),
                'cash' => $request->input('cash') ?? 0,
                'card' => $request->input('card') ?? 0,
                'plin' => $request->input('plin') ?? 0,
                'isBankPayment' => $request->input('isBankPayment'),
                'routeVoucher' => $routeVoucher,
                'numberVoucher' => $numberVoucher,
                'comment' => $request->input('comment') ?? '-',
            ]);
        } else if ($sale->paymentType == Constants::SALE_CREDITO) {
            $sumCommitments = array_sum(array_column($request->input('commitments'), 'price'));
            if (round($sumCommitments, 4) != round($sale->total, 4)) {
                return response()->json(['error' => 'La suma de los compromisos no coincide con el total ' . $sale->total . ' diferencia ' . ($sale->total - $sumCommitments)], 422);
            }

            $commitments = $request->input('commitments');
            foreach ($commitments as $index => $commitmentData) {
                $commitment = $sale->commitments()->find($commitmentData['id']);
                if ($commitment) {
                    $commitment->update([
                        'numberQuota' => $index + 1,
                        'price' => $commitmentData['price'],
                        'balance' => $commitmentData['price'],
                        'status' => Constants::COMMITMENT_PENDING,
                        'payment_date' => Carbon::parse($sale->budgetSheet->attention->arrivalDate)->addDays($commitmentData['paymentDate']),
                    ]);
                } else {
                    Commitment::create([
                        'numberQuota' => $index + 1,
                        'price' => $commitmentData['price'],
                        'balance' => $commitmentData['price'],
                        'status' => Constants::COMMITMENT_PENDING,
                        'payment_date' => Carbon::today()->addDays($commitmentData['paymentDate']),
                        'sale_id' => $sale->id,
                    ]);
                }
            }
        }

        $taxableOperation = 0;
        foreach ($request->saleDetails as $saleDetail) {
            $sale->details()->updateOrCreate(
                ['id' => $saleDetail['id']],
                [
                    'description' => $saleDetail['description'],
                    'unit' => $saleDetail['unit'],
                    'quantity' => $saleDetail['quantity'],
                    'unitValue' => $saleDetail['unitValue'],
                    'unitPrice' => $saleDetail['unitPrice'],
                    'discount' => $saleDetail['discount'] ?? 0,
                    'subTotal' => $saleDetail['subTotal'],
                ]
            );
            $taxableOperation += $saleDetail['subTotal'];
        }

        $igv = $taxableOperation * Constants::IGV;
        $total = $taxableOperation + $igv;

        $sale->update([
            'taxableOperation' => $taxableOperation,
            'igv' => $igv,
            'total' => $total,
        ]);

        $sale = Sale::find($sale->id);
        $budgetSheet->status = Constants::BUDGET_SHEET_FACTURADO;
        $budgetSheet->save();

        return response()->json(SaleResource::make($sale)->withBudgetSheet());
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
    public function pruebaFacturador()
    {
        // Aquí puedes pasar datos a la vista si lo necesitas, pero por ahora solo devuelve la vista.
        return view('pruebaFacturador');
    }
}
