<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AmortizationResource;
use App\Models\Amortization;
use App\Models\budgetSheet;
use App\Models\Commitment;
use App\Models\ConceptPay;
use App\Models\Moviment;
use App\Models\Sale;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AmortizationController extends Controller
{
    public function amortizationsByCommitmentId(Request $request, int $id)
    {
        $validator = validator()->make($request->query(), [
            'per_page' => 'nullable|integer',
            'all' => 'nullable|string|in:true,false',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $per_page = $request->query('per_page', 10);
        $all = $request->query('all') == 'true';

        $amortizations = Amortization::where('commitment_id', $id)->orderBy('created_at', 'desc');

        if ($all) {
            $amortizations = $amortizations->get();
        } else {
            $amortizations = $amortizations->simplePaginate($per_page);
        }

        return response()->json($amortizations);
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/amortization",
     *     tags={"Amortization"},
     *     summary="Create a amortization",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(ref="#/components/schemas/AmortizationRequest")
     *           )
     *       ),
     *     @OA\Response(response=200, description="Successful operation" ),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(Request $request)
    {
//        VALIDATOR
        $validator = validator()->make($request->all(), [
            'paymentDate' => 'required|date',
            'routeVoucher.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'numberVoucher' => 'nullable|string',
            'yape' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'cash' => 'nullable|numeric',
            'plin' => 'nullable|numeric',
            'card' => 'nullable|numeric',
            'comment' => 'nullable|string',
            'isBankPayment' => 'required|in:0,1',

            'bank_id' => 'nullable|exists:banks,id',
            'person_id' => 'required|exists:people,id',
            'commitment_id' => 'required|exists:commitments,id',
        ]);

//        VALIDATOR FAILS
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

//        FIND MOVEMENT INCOME ACTIVE
        $movCaja = Moviment::where('status', 'Activa')->where('paymentConcept_id', 1)->first();

//        IF NOT MOVEMENT INCOME ACTIVE
        if (!$movCaja) {
//            IF PAYMENT CONCEPT IS NOT APERTURA DE CAJA
            if ($request->input('paymentConcept_id') != 1) {
                return response()->json([
                    "message" => "Debe Aperturar Caja",
                ], 422);
            }
        } else {
//            IF PAYMENT CONCEPT IS APERTURA DE CAJA
            if ($request->input('paymentConcept_id') == 1) {
                return response()->json([
                    "message" => "Caja Ya Aperturada",
                ], 422);
            }
        }

        $tipo = 'M001';

        $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);

        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int)$resultado;

//        DATA
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
        $efectivo = $request->input('cash') ?? 0;
        $yape = $request->input('yape') ?? 0;
        $plin = $request->input('plin') ?? 0;
        $tarjeta = $request->input('card') ?? 0;
        $deposito = $depositAmount ?? 0;

//        TOTAL
        $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

//       COMMITMENT
        $commitment = Commitment::find($request->input('commitment_id'));

        if ($commitment->balance == 0) {
            return response()->json(["error" => "El compromiso ya fue pagado",], 422);
        }

        if ($total == 0) {
            return response()->json([
                "error" => "El monto a pagar no puede ser 0",
            ], 422);
        }

        if ($commitment->balance < $total) {
            return response()->json([
                "error" => "El monto a pagar no puede ser mayor al saldo pendiente de S/ " . number_format($commitment->balance, 2),
            ], 422);
        }

//        PAYMENT CONCEPT
        $paymentConcept = ConceptPay::find(7);

        $image = $request->file('routeVoucher');

        $bankPayment = $request->input('isBankPayment');
//        IF IMAGE
        if ($image) {
            $bankPayment = 1;
        }

//        DATA MOVIMENT
        $dataMoviment = [
            'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentDate' => $request->input('paymentDate'),
            'total' => $total ?? 0,
            'yape' => $request->input('yape') ?? 0,
            'deposit' => $depositAmount ?? 0,
            'cash' => $request->input('cash') ?? 0,
            'card' => $request->input('card') ?? 0,
            'plin' => $request->input('plin') ?? 0,

            'isBankPayment' => $bankPayment,
            'routeVoucher' => $routeVoucher,
            'numberVoucher' => $numberVoucher,
            'typeDocument' => $paymentConcept->type,

            'comment' => $request->input('comment') ?? '-',
            'status' => 'Generada',
            'paymentConcept_id' => 7,

            'person_id' => $request->input('person_id'),
            'user_id' => auth()->id(),
            'bank_id' => $bank_id,
            'sale_id' => $commitment->sale->id,
        ];

//        CREATE MOVIMENT
        $object = Moviment::create($dataMoviment);

//        CREATE IMAGE
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
            $object->routeVoucher = $rutaImagen;
            $object->save();
            Log::info('Imagen guardada en la base de datos con ruta: ' . $rutaImagen);
        }

//        GENERATE SEQUENTIAL NUMBER FOR AMORTIZATION
        $tipo = 'AMRT';
        $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);

//        CONSULTA PARA OBTENER EL SIGUIENTE NUM
        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM amortizations WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int)$resultado;

//        DATA AMORTIZATION
        $dataAmortization = [
            'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'amount' => $total,
//            'status' => 'Pendiente',
            'paymentDate' => $request->input('paymentDate'),
            'moviment_id' => $object->id,
            'commitment_id' => $request->input('commitment_id'),
        ];

//        CREATE AMORTIZATION
        $amortization = Amortization::create($dataAmortization);

//        UPDATE COMMITMENT
        $commitment->balance -= $total;
        $commitment->amount += $total;
        $commitment->status = $commitment->balance == 0 ? Constants::COMMITMENT_PAGADO : Constants::COMMITMENT_PENDIENTE;
        $commitment->save();

        $commitments = Commitment::where('sale_id', $commitment->sale_id)->get();
        $anyCommitmentPending = false;
        foreach ($commitments as $commitment) {
            if ($commitment->status == Constants::COMMITMENT_PENDIENTE) {
                $anyCommitmentPending = true;
                break;
            }
        }
        if (!$anyCommitmentPending) {
            $sale = Sale::find($commitment->sale_id);
            if ($sale->budget_sheet_id) {
                $sale->budgetSheet->status = $sale->budgetSheet->status == Constants::BUDGET_SHEET_FACTURADO ? Constants::BUDGET_SHEET_FACTURADO : Constants::BUDGET_SHEET_PAGADO;
                $sale->budgetSheet->save();
            }
            $sale->status = Constants::SALE_PAGADO;
            $sale->save();
        }

        $amortization = Amortization::find($amortization->id);

        return response()->json(AmortizationResource::make($amortization));
    }
}
