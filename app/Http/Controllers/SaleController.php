<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestSale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Amortization;
use App\Models\budgetSheet;
use App\Models\Cash;
use App\Models\Commitment;
use App\Models\Moviment;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
     *     @OA\Parameter( name="from", in="query", description="Filter by from", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter( name="to", in="query", description="Filter by to", @OA\Schema(type="string", format="date")),
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
        if ($request->all == 'true') {
            return [
                'data' => $this->getFilteredResults(
                    Sale::class,
                    $request,
                    Sale::filters,
                    Sale::sorts,
                    SaleResource::class
                )->original,
                'links' => null,
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'links' => [
                        ['url' => null, 'label' => '&laquo; Previous', 'active' => false],
                        ['url' => null, 'label' => 'Next &raquo;', 'active' => false],
                    ],
                    'path' => 'https://develop.garzasoft.com/tecnimotors-backend/public/api/noteReason',
                    'per_page' => 100,
                    'to' => 10,
                    'total' => 10,
                ],
            ];
        }
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
            $subtotal += ($saleDetail['unitValue']) * $saleDetail['quantity'];
        }
        $igv = $subtotal * Constants::IGV;
        $total = $subtotal + $igv;


        $cashId = 2;
        $query = Sale::where('documentType', $request->documentType)
            ->where('cash_id', $cashId);

        $data = [
            'number' => $this->nextCorrelativeQuery($query, 'number'),
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

            'retencion' => $request->input('saleType') === Constants::SALE_RETENCION ? $request->input('retencion') : 0,

            'cuentabn' => '00231124403',

            'cash_id' => $cashId,
            'user_id' => auth()->id(),
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

            if (round($sale->total - $total, 1) != 0) {
                return response()->json([
                    "error" => "El monto a pagar no coincide con el total " . round($sale->total, 1) .
                        " diferencia " . round($sale->total - $total, 1),
                ], 422);
            }

            //            THEN SAVE SALE
            $sale->save();
            $commitment = Commitment::create([
                'numberQuota' => 1,
                'price' => $sale->total,
                'balance' => 0,
                'amount' => $sale->total,
                'status' => Constants::COMMITMENT_PAGADO,
                'payment_type' => Constants::COMMITMENT_CONTADO,
                'payment_date' => today(),
                'sale_id' => $sale->id,
            ]);

            //            MOVEMENT CREATION
            $tipo = 'M001';
            $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);
            $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
            $siguienteNum = (int) $resultado;

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
                'cash' => $request->input('effective') ?? 0,
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
            $sale->save();

            //            AMORTIZATION CREATION
            $tipo = 'AMRT';
            $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);
            $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM amortizations WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
            $siguienteNum = (int) $resultado;

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
            if (round($sale->total - $sumCommitments, 1) != 0) {
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
                'discount' => 0,
                'subTotal' => $saleDetail['subTotal'],
                'sale_id' => $sale->id,
            ]);
            $taxableOperation += $saleDetail['unitValue'] * $saleDetail['quantity'];
        }

        $igv = $taxableOperation * Constants::IGV;
        $total = $taxableOperation + $igv;

        $igv = round($taxableOperation * Constants::IGV, 2);
        $total = round($taxableOperation + $igv, 2);

        $sale->update([
            'taxableOperation' => round($taxableOperation, 2),
            'igv' => $igv,
            'total' => $total,
        ]);

        $sale->save();

        $this->updateFullNumber($sale);

        $sale = Sale::find($sale->id);
        if ($budgetSheet) {
            $budgetSheet->status = Constants::BUDGET_SHEET_FACTURADO;
            $budgetSheet->save();
        }

        switch ($request->input('documentType')) {
            case 'BOLETA':
                Log::info($this->declararBoletaFactura($sale->id, 3));
                break;
            case 'FACTURA':
                $this->declararBoletaFactura($sale->id, 2);
                break;
            default:
                Log::info("documentType NO DEFINIDO id venta: $sale->id");
                break;
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
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

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
     public function update(UpdateSaleRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $sale = Sale::with(['saleDetails', 'commitments.amortizations', 'moviment'])->findOrFail($id);

            // === 1) Encabezado ===
            $sale->fill([
                'paymentDate' => $request->paymentDate,
                'saleType' => $request->saleType,
                'detractionCode' => $request->saleType === Constants::SALE_DETRACCION ? ($request->detractionCode ?? '') : '',
                'detractionPercentage' => $request->saleType === Constants::SALE_DETRACCION ? ($request->detractionPercentage ?? 0) : 0,
                'paymentType' => $request->paymentType,
                'status' => $sale->status ?? Constants::SALE_PENDIENTE,
                'person_id' => $request->person_id,
                'budget_sheet_id' => $request->budget_sheet_id,
                'retencion' => $request->saleType === Constants::SALE_RETENCION ? ($request->retencion ?? 0) : 0,
                'cuentabn' => $request->cuentabn ?? ($sale->cuentabn ?? '00231124403'),
                'cash_id' => 2,
                'user_id' => auth()->id(),
            ]);

            // === 2) Sincronizar Detalles ===
            $this->syncDetails($sale, $request->saleDetails);

            // === 3) Totales del request ===
            $sale->taxableOperation = round($request->__calc_taxable, 2);
            $sale->igv = $request->__calc_igv;
            $sale->total = $request->__calc_total;
            $sale->save();

            // === 4) Flujo de pago ===
            if ($sale->paymentType === Constants::SALE_CONTADO) {
                // compromisos → si existe actualizo, sino creo
                $commitment = $sale->commitments()->first();
                if ($commitment) {
                    // actualizar y borrar amortizaciones ligadas (para regenerarlas)
                    $commitment->amortizations()->delete();
                    $commitment->update([
                        'price' => $sale->total,
                        'balance' => 0,
                        'amount' => $sale->total,
                        'status' => Constants::COMMITMENT_PAGADO,
                        'payment_type' => Constants::COMMITMENT_CONTADO,
                        'payment_date' => today(),
                    ]);
                } else {
                    $commitment = $sale->commitments()->create([
                        'numberQuota' => 1,
                        'price' => $sale->total,
                        'balance' => 0,
                        'amount' => $sale->total,
                        'status' => Constants::COMMITMENT_PAGADO,
                        'payment_type' => Constants::COMMITMENT_CONTADO,
                        'payment_date' => today(),
                    ]);
                }

                // movimiento → update si existe, create si no
                $movement = $sale->moviment;
                if (!$movement) {
                    $movement = $sale->moviment()->create([
                        'sequentialNumber' => $this->nextSequential('M001', 'moviments'),
                    ]);
                }
                $movement->fill([
                    'paymentDate' => now(),
                    'total' => $commitment->price,
                    'yape' => $request->yape,
                    'deposit' => $request->deposit,
                    'nro_operation' => $request->nro_operation,
                    'cash' => $request->effective,
                    'card' => $request->card,
                    'plin' => $request->plin,
                    'isBankPayment' => $request->isBankPayment,
                    'numberVoucher' => $request->numberVoucher,
                    'bank_id' => $request->bank_id,
                    'comment' => $request->comment ?? '-',
                    'status' => 'Generada',
                    'paymentConcept_id' => 7,
                    'person_id' => $request->person_id,
                    'user_id' => auth()->id(),
                ])->save();

                // voucher
                if ($request->hasFile('routeVoucher')) {
                    $file = $request->file('routeVoucher');
                    $filename = now()->format('YmdHis') . '_' . $file->getClientOriginalName();
                    $movement->routeVoucher = Storage::url($file->storeAs('public/photosVouchers', $filename));
                    $movement->save();
                }

                // actualizar venta con medios
                $sale->fill([
                    'yape' => $movement->yape,
                    'deposit' => $movement->deposit,
                    'nro_operation' => $movement->nro_operation,
                    'effective' => $movement->cash,
                    'card' => $movement->card,
                    'plin' => $movement->plin,
                    'isBankPayment' => $movement->isBankPayment,
                    'bank_id' => $movement->bank_id,
                    'numberVoucher' => $movement->numberVoucher,
                    'routeVoucher' => $movement->routeVoucher,
                    'comment' => $movement->comment,
                ])->save();

                // amortización → siempre regenerar
                // $movement->amortizations()->delete();
                $commitment->amortizations()->delete();
                // $movement->amortizations()->create([
                //     'sequentialNumber' => $this->nextSequential('AMRT', 'amortizations'),
                //     'amount' => $commitment->price,
                //     'paymentDate' => now(),
                //     'commitment_id' => $commitment->id,
                // ]);

            } else { // === CRÉDITO ===
                // eliminar movement + amorts si existían
                if ($sale->moviment) {
                    // $sale->moviment->amortizations()->delete();
                    $sale->moviment->delete();
                }

                // sincronizar compromisos por id
                $this->syncCommitments($sale, $request->commitments);
            }

            // === 5) Hoja de presupuesto ===
            if ($sale->budget_sheet_id) {
                $bs = budgetSheet::find($sale->budget_sheet_id);
                if ($bs) {
                    $bs->status = Constants::BUDGET_SHEET_FACTURADO;
                    $bs->save();
                }
            }

            $sale->refresh();

            switch ($sale->documentType) {
                case 'BOLETA':
                    Log::info($this->declararBoletaFactura($sale->id, 3));
                    break;
                case 'FACTURA':
                    $this->declararBoletaFactura($sale->id, 2);
                    break;
                default:
                    Log::info("documentType es un Ticket, id venta: $sale->id");
                    break;
            }

            $sale = SaleResource::make($sale)->withBudgetSheet();
            return response()->json($sale);
        });
    }

    /** Helpers **/

    private function syncDetails(Sale $sale, array $details)
    {
        $existing = $sale->saleDetails->keyBy('id');
        $incoming = collect($details);

        // update o create
        foreach ($incoming as $row) {
            if (!empty($row['id']) && $existing->has($row['id'])) {
                $existing[$row['id']]->update($row);
                $existing->forget($row['id']);
            } else {
                $sale->saleDetails()->create($row);
            }
        }

        // los que no vinieron → delete
        foreach ($existing as $detail) {
            $detail->delete();
        }
    }

    private function syncCommitments(Sale $sale, array $commitments)
    {
        $existing = $sale->commitments->keyBy('id');
        foreach ($commitments as $i => $c) {
            $data = [
                'numberQuota' => $i + 1,
                'price' => $c['price'],
                'balance' => $c['price'],
                'amount' => 0,
                'status' => Constants::COMMITMENT_PENDING,
                'payment_date' => Carbon::today()->addDays($c['paymentDate']),
                'payment_type' => Constants::COMMITMENT_CREDITO,
            ];
            if (!empty($c['id']) && $existing->has($c['id'])) {
                $existing[$c['id']]->update($data);
                $existing->forget($c['id']);
            } else {
                $sale->commitments()->create($data);
            }
        }
        // los que no vinieron → borrar con amortizaciones
        foreach ($existing as $old) {
            $old->amortizations()->delete();
            $old->delete();
        }
    }


    private function nextSequential(string $prefix, string $table): string
    {
        $prefix = str_pad($prefix, 4, '0', STR_PAD_RIGHT);
        $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE('-', sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS n
            FROM {$table} WHERE SUBSTRING(sequentialNumber,1,4)=?";
        $n = DB::select($sql, [$prefix])[0]->n ?? 1;
        return $prefix . '-' . str_pad($n, 8, '0', STR_PAD_LEFT);
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
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        if ($sale->status === Constants::SALE_FACTURADO) {
            return response()->json(['message' => 'Sale already invoiced'], 422);
        }

        $sale->delete();
        return response()->json(['message' => 'Sale deleted']);
    }

    public function pruebaFacturador()
    {
        // Aquí puedes pasar datos a la vista si lo necesitas, pero por ahora solo devuelve la vista.
        return view('pruebaFacturador');
    }

    public function getArchivosDocument($idventa, $typeDocument)
    {
        // Habilitar CORS para un origen específico
        header("Access-Control-Allow-Origin: https://tecnimotors.vercel.app"); // Permitir solo este origen
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Permitir métodos HTTP específicos
        header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permitir tipos de encabezados específicos

        // Si es una solicitud OPTIONS (preflight), responde sin ejecutar más lógica
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200); // Código de éxito
            exit; // Termina el script aquí
        }

        $funcion = 'buscarNumeroSolicitud';
        $url = 'https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php?funcion=' . $funcion . "&typeDocument=" . $typeDocument;

        // Parámetros para la solicitud
        $params = http_build_query(['idventa' => $idventa]);

        // Inicializamos cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '&' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutamos la solicitud y obtenemos la respuesta
        $response = curl_exec($ch);

        // Cerramos cURL
        curl_close($ch);

        // Verificamos si la respuesta es válida
        if ($response !== false) {
            // Decodificamos la respuesta JSON
            $data = json_decode($response, true);

            // Verificamos si la respuesta contiene la información del archivo XML
            if (isset($data['xml'])) {
                $xmlFile = $data['xml'];

                // Ruta completa del archivo XML
                $fileUrl = 'https://develop.garzasoft.com:81/tecnimotors-facturador/ficheros/' . $xmlFile;

                // Obtener el contenido del archivo XML
                $fileContent = file_get_contents($fileUrl);

                if ($fileContent !== false) {
                    // Forzar la descarga del archivo XML
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/xml');
                    header('Content-Disposition: attachment; filename="' . basename($xmlFile) . '"');
                    header('Content-Length: ' . strlen($fileContent));

                    // Enviar el contenido del archivo
                    echo $fileContent;
                    exit;
                } else {
                    echo 'Error al descargar el archivo XML.';
                }
            } else {
                echo 'Archivo XML no encontrado.';
            }
        } else {
            echo 'Error en la solicitud.';
        }
    }

    public function declararBoletaFactura($idventa, $idtipodocumento)
    {
        $empresa_id = 1;

        $moviment = Sale::find($idventa);

        if (!$moviment) {
            return response()->json(['message' => 'VENTA NO ENCONTRADA'], 422);
        }
        // if ($moviment->status_facturado != 'Pendiente') {
        //     return response()->json(['message' => 'VENTA NO SE ENCUENTRA EN PENDIENTE DE ENVÍO'], 422);
        // }

        // Definir la función de acuerdo al tipo de documento
        if ($idtipodocumento == 3) {
            $funcion = "enviarBoleta";
        } else {
            $funcion = "enviarFactura";
        }

        // Construir la URL con los parámetros
        $url = "https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php";
        $params = [
            'funcion' => $funcion,
            'idventa' => $idventa,
            'empresa_id' => $empresa_id,
        ];
        $url .= '?' . http_build_query($params);

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si ocurrió algún error
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            // Registrar el error en el log
            Log::error("Error en cURL al enviar VENTA. ID venta: $idventa,$funcion Error: $error");
            // echo 'Error en cURL: ' . $error;
        } else {
            // Registrar la respuesta en el log
            Log::error("Respuesta recibida de VENTA para ID venta: $idventa,$funcion Respuesta: $response");
            // Mostrar la respuesta
            // echo 'Respuesta: ' . $response;
        }

        // Cerrar cURL
        curl_close($ch);
        // Log del cierre de la solicitud
        Log::info("Solicitud de VENTA finalizada para ID venta: $idventa. $funcion");

        $moviment->status_facturado = Constants::SALE_STATUS_ENVIADO;
        $moviment->save();

        return response()->json($moviment, 200);
    }

    public function updateFullNumber($sale): void
    {
        $documentTypePrefixes = [
            Constants::SALE_TICKET => 'T',
            Constants::SALE_BOLETA => 'B',
            Constants::SALE_FACTURA => 'F',
            Constants::SALE_NOTA_CREDITO_BOLETA => 'FC',
            Constants::SALE_NOTA_CREDITO_FACTURA => 'BC',
        ];
        $fullNumber = $documentTypePrefixes[$sale->documentType] . $sale->cash?->series . '-' . $sale->number;
        $sale->update(['fullNumber' => $fullNumber]);
        $sale->save();
    }
    public function sendemail(Request $request, $id)
    {
        // Base URL de la API
        $api_url = "https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php";

        // Recuperar datos del request
        $emails = $request->input('emails'); // Array de correos electrónicos
        $comentario = $request->input('comentario', ''); // Comentario opcional
        $funcion = "enviaremail";

        // Log de la solicitud enviada
        $url = "https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php";
        // Construir los parámetros para la solicitud
        $params = [
            'funcion' => $funcion,
            'token' => 'pdfD3scargar',
            'idventa' => $id,
            'comentario' => $comentario,
        ];

        // Añadir los correos al array de parámetros
        if (is_array($emails)) {
            foreach ($emails as $index => $email) {
                $params["emails[$index]"] = $email;
            }
        }
        $url .= '?' . http_build_query($params);

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si ocurrió algún error
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            // Registrar el error en el log
            Log::error("Error en cURL al enviar VENTA. ID venta: $id,$funcion Error: $error");
            // echo 'Error en cURL: ' . $error;
        } else {
            // Registrar la respuesta en el log
            Log::error("Respuesta recibida de VENTA para ID venta: $id,$funcion Respuesta: $response");
            // Mostrar la respuesta
            // echo 'Respuesta: ' . $response;
        }

        // Cerrar cURL
        curl_close($ch);

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }
}
