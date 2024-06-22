<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConceptPay;
use App\Models\Moviment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MovimentController extends Controller
{
    /**
     * Get all Movimentes
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/moviment",
     *     tags={"Moviment"},
     *     security={{"bearerAuth":{}}},
     *        @OA\Parameter(
     *         name="paymentConcept_id",
     *         in="query",
     *         description="Nombre Concepto Pago",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="numberBudget",
     *         in="query",
     *         description="Numero Presupuesto",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of active Movimentes",
     *         @OA\JsonContent(
     *             @OA\Property(property="detalle", type="object",
     *                 @OA\Property(property="MovCajaApertura", ref="#/components/schemas/MovimentRequest"),
     *                 @OA\Property(property="MovCajaCierre", ref="#/components/schemas/MovimentRequest"),
     *                 @OA\Property(property="MovCajaInternos", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MovimentRequest")),
     *                     @OA\Property(property="first_page_url", type="string", example="http://localhost/tecnimotors-backend/public/api/moviment?page=1"),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="next_page_url", type="string", example="null"),
     *                     @OA\Property(property="path", type="string", example="http://localhost/tecnimotors-backend/public/api/moviment"),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="prev_page_url", type="string", example="null"),
     *                     @OA\Property(property="to", type="integer", example=2)
     *                 ),
     *                 @OA\Property(property="resumenCaja", type="object",
     *                     @OA\Property(property="total_ingresos", type="string", example="150.50"),
     *                     @OA\Property(property="total_egresos", type="string", example="150.50"),
     *                     @OA\Property(property="efectivo_ingresos", type="string", example="50.00"),
     *                     @OA\Property(property="efectivo_egresos", type="string", example="50.00"),
     *                     @OA\Property(property="yape_ingresos", type="string", example="20.00"),
     *                     @OA\Property(property="yape_egresos", type="string", example="20.00"),
     *                     @OA\Property(property="plin_ingresos", type="string", example="0.00"),
     *                     @OA\Property(property="plin_egresos", type="string", example="50.00"),
     *                     @OA\Property(property="tarjeta_ingresos", type="string", example="0.50"),
     *                     @OA\Property(property="tarjeta_egresos", type="string", example="0.50"),
     *                     @OA\Property(property="deposito_ingresos", type="string", example="30.00"),
     *                     @OA\Property(property="deposito_egresos", type="string", example="30.00")
     *                 )
     *             )
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
        $paymentConcept = $request->input('paymentConcept_id') ?? '';
        $numberBudget = $request->input('numberBudget') ?? '';

        $movCaja = Moviment::where('status', 'Activa')
            ->where('paymentConcept_id', 1)
            ->first();

        $data = [];
        if ($movCaja) {
            $data = $this->detalleCajaAperturada($movCaja->id, $paymentConcept, $numberBudget)->original;
            // $data = [];
        }
        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/movimentAperturaCierre",
     *     summary="Store a new moviment",
     *     tags={"Moviment"},
     *     description="Create a new moviment Apertura/Cierre",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Moviment data",
     *         @OA\JsonContent(

     *             @OA\Property(
     *                 property="paymentDate",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha de pago",
     *                 nullable=true,
     *                 example="2023-06-17"
     *             ),
     *             @OA\Property(
     *                 property="paymentConcept_id",
     *                 type="integer",
     *                 description="ID del concepto de pago",
     *                 nullable=true,
     *                 example=1
     *             ),
     *                 @OA\Property(
     *                 property="routeVoucher",
     *                  type="string",
     *                 format="binary",
     *                 description="Imagen File",
     *                 nullable=false
     *             ),
     *                 @OA\Property(
     *                 property="numberVoucher",
     *                  type="string",
     *                  description="Numero del voucher",
     *             ),
     *             @OA\Property(
     *                 property="isBankPayment",
     *                 type="integer",
     *                 description="0 Desactivado / 1 Activado",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="yape",
     *                 type="number",
     *                 format="decimal",
     *                 description="Pago por Yape",
     *                 nullable=true,
     *                 example=20.00
     *             ),
     *             @OA\Property(
     *                 property="deposit",
     *                 type="number",
     *                 format="decimal",
     *                 description="Depósito",
     *                 nullable=true,
     *                 example=30.00
     *             ),
     *             @OA\Property(
     *                 property="cash",
     *                 type="number",
     *                 format="decimal",
     *                 description="Efectivo",
     *                 nullable=true,
     *                 example=50.00
     *             ),
     *             @OA\Property(
     *                 property="plin",
     *                 type="number",
     *                 format="decimal",
     *                 description="Efectivo",
     *                 nullable=true,
     *                 example=50.00
     *             ),
     *             @OA\Property(
     *                 property="card",
     *                 type="number",
     *                 format="decimal",
     *                 description="Pago por tarjeta",
     *                 nullable=true,
     *                 example=0.50
     *             ),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 description="Comentario",
     *                 nullable=true,
     *                 example="Pago parcial"
     *             ),

     *             @OA\Property(
     *                 property="person_id",
     *                 type="integer",
     *                 description="ID de persona",
     *                 nullable=true,
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="bank_id",
     *                 type="integer",
     *                 description="ID del banko",
     *                 nullable=true,
     *                 example=1
     *             ),

     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moviment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MovimentRequest")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Some fields are required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function aperturaCierre(Request $request)
    {

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

            'paymentConcept_id' => 'required|in:1,2|exists:concept_pays,id',
            'bank_id' => 'nullable|exists:banks,id',
            'person_id' => 'required|exists:people,id',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

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
        $letra = 'M';
        $status = '';
        $typeDocument = 'Ingreso';
        if ($request->input('paymentConcept_id') == 1) {
            $letra = 'A';
            $status = 'Activa';
            $typeDocument = 'Ingreso';

        } else if (($request->input('paymentConcept_id') == 2)) {
            $letra = 'C';
            $status = 'Inactiva';
            $typeDocument = 'Egreso';

            $movCaja = Moviment::where('status', 'Activa')
                ->where('paymentConcept_id', 1)
                ->first();
            $movCaja->status = 'Inactiva';
            $movCaja->save();
        }

        $tipo = $letra . '001';

        $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);

        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

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

        $efectivo = $request->input('cash') ?? 0;
        $yape = $request->input('yape') ?? 0;
        $plin = $request->input('plin') ?? 0;
        $tarjeta = $request->input('card') ?? 0;
        $deposito = $depositAmount ?? 0;

        $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

        $data = [

            'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentDate' => $request->input('paymentDate'),
            'total' => $total ?? 0,
            'yape' => $request->input('yape') ?? 0,
            'deposit' => $depositAmount ?? 0,
            'cash' => $request->input('cash') ?? 0,
            'card' => $request->input('card') ?? 0,
            'plin' => $request->input('plin') ?? 0,
            'typeDocument' => $typeDocument,

            'isBankPayment' => $request->input('isBankPayment'),
            'routeVoucher' => $routeVoucher,
            'numberVoucher' => $numberVoucher,

            'comment' => $request->input('comment') ?? '-',
            'status' => $status,
            'paymentConcept_id' => $request->input('paymentConcept_id'),

            'person_id' => $request->input('person_id'),
            'user_id' => auth()->id(),
            'bank_id' => $bank_id,
        ];

        $object = Moviment::create($data);

        $image = $request->file('routeVoucher');

        if ($image) {
            $file = $image;
            $currentTime = now();
            $filename = $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/photosVouchers', $filename);
            $rutaImagen = Storage::url($path);
            $object->routeVoucher = $rutaImagen;
            $object->save();
        }

        $object = Moviment::with(['bank', 'paymentConcept',
            'person', 'user.worker.person'])->find($object->id);
        return response()->json($object, 200);

    }

    public function detalleCajaAperturada($id, $paymentConcept = '', $numberBudget = '')
    {
        $movCajaAperturada = Moviment::where('id', $id)->where('paymentConcept_id', 1)
            ->first();

        if (!$movCajaAperturada) {
            return response()->json([
                "message" => "Movimiento de Apertura no encontrado",
            ], 404);
        }

        $movCajaCierre = Moviment::where('id', '>', $movCajaAperturada->id)
            ->where('paymentConcept_id', 2)
            ->orderBy('id', 'asc')->first();

        if ($movCajaCierre == null) {
            //CAJA ACTIVA
            $query = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
                ->where('id', '>=', $movCajaAperturada->id)
                ->orderBy('id', 'desc')
                ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet']);

            $query->where(function ($query) use ($paymentConcept, $numberBudget) {

                if ($paymentConcept !== '') {
                    $query->whereHas('paymentConcept', function ($query) use ($paymentConcept) {
                        $query->where('name', 'like', "%$paymentConcept%");
                    });
                }
                if ($numberBudget !== '') {
                    $query->whereHas('budgetSheet', function ($query) use ($numberBudget) {
                        $query->where('number', 'like', "%$numberBudget%");
                    });
                }

            });

            // Ejecutar la consulta paginada
            $movimientosCaja = $query->paginate(15);

            $movimientosCaja = [
                'current_page' => $movimientosCaja->currentPage(),
                'data' => $movimientosCaja->items(), // Los datos paginados
                'total' => $movimientosCaja->total(), // El total de registros
                'first_page_url' => $movimientosCaja->url(1),
                'from' => $movimientosCaja->firstItem(),
                'next_page_url' => $movimientosCaja->nextPageUrl(),
                'path' => $movimientosCaja->path(),
                'per_page' => $movimientosCaja->perPage(),
                'prev_page_url' => $movimientosCaja->previousPageUrl(),
                'to' => $movimientosCaja->lastItem(),
            ];

            $resumenCaja = Moviment::selectRaw('
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.total ELSE 0 END), 0.00) as total_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.total ELSE 0 END), 0.00) as total_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_egresos')
                ->leftJoin('concept_pays as cp', 'moviments.paymentConcept_id', '=', 'cp.id')
                ->where('moviments.id', '>=', $movCajaAperturada->id)
                ->first();

            $movCajaCierreArray = null;
        } else {
            $movimientosCaja = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
                ->where('id', '>=', $movCajaAperturada->id)
                ->where('branchOffice_id', $movCajaAperturada->branchOffice_id)
                ->where('id', '<', $movCajaCierre->id)
                ->orderBy('id', 'desc')
                ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])
                ->simplePaginate();

            $resumenCaja = Moviment::selectRaw('
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.total ELSE 0 END), 0.00) as total_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.total ELSE 0 END), 0.00) as total_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_egresos')
                ->leftJoin('concept_pays as cp', 'moviments.paymentConcept_id', '=', 'cp.id')

                ->where('moviments.id', '>=', $movCajaAperturada->id)
                ->where('moviments.id', '<', $movCajaCierre->id)

                ->first();

            $forma_pago = DB::select('SELECT obtenerFormaPagoPorCaja(:id) AS forma_pago', ['id' => $movCajaCierre->id]);

        }

        return response()->json([

            'MovCajaApertura' => $movCajaAperturada,
            'MovCajaCierre' => $movCajaCierre,
            'MovCajaInternos' => $movimientosCaja,

            "resumenCaja" => $resumenCaja ?? null,
        ]);

    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/moviment/{id}",
     *     summary="Get a moviment by ID",
     *     tags={"Moviment"},
     *     description="Retrieve a moviment by its ID",
     * security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the moviment to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moviment found",
     *        @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/MovimentRequest"
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $object = Moviment::with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])->find($id);

        if (!$object) {
            return response()->json(['message' => 'Moviment not found'], 422);
        }

        return response()->json($object, 200);
    }

    /**
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/moviment/{id}",
     *     summary="Delete a Moviment",
     *     tags={"Moviment"},
     *     description="Delete a Moviment by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Moviment to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moviment deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *        @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */

    public function destroy($id)
    {
        $object = Moviment::find($id);
        if (!$object) {
            return response()->json(['message' => 'Moviment not found'], 422);
        }
        $object->delete();
        $object->save();
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/moviment",
     *     summary="Store a new moviment",
     *     tags={"Moviment"},
     *     description="Create a new moviment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Moviment data",
     *         @OA\JsonContent(

     *             @OA\Property(
     *                 property="paymentDate",
     *                 type="string",
     *                 format="date-time",
     *                 description="Fecha de pago",
     *                 nullable=true,
     *                 example="2023-06-17"
     *             ),
     *             @OA\Property(
     *                 property="paymentConcept_id",
     *                 type="integer",
     *                 description="ID del concepto de pago",
     *                 nullable=true,
     *                 example=3
     *             ),
     *             @OA\Property(
     *                 property="routeVoucher",
     *                  type="string",
     *                 format="binary",
     *                 description="Imagen File",
     *                 nullable=false
     *             ),
     *             @OA\Property(
     *                 property="numberVoucher",
     *                  type="string",
     *                  description="Numero del voucher",
     *             ),
     *             @OA\Property(
     *                 property="isBankPayment",
     *                 type="integer",
     *                 description="0 Desactivado / 1 Activado",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="yape",
     *                 type="number",
     *                 format="decimal",
     *                 description="Pago por Yape",
     *                 nullable=true,
     *                 example=20.00
     *             ),
     *             @OA\Property(
     *                 property="deposit",
     *                 type="number",
     *                 format="decimal",
     *                 description="Depósito",
     *                 nullable=true,
     *                 example=30.00
     *             ),
     *             @OA\Property(
     *                 property="cash",
     *                 type="number",
     *                 format="decimal",
     *                 description="Efectivo",
     *                 nullable=true,
     *                 example=50.00
     *             ),
     *             @OA\Property(
     *                 property="plin",
     *                 type="number",
     *                 format="decimal",
     *                 description="Efectivo",
     *                 nullable=true,
     *                 example=50.00
     *             ),
     *             @OA\Property(
     *                 property="card",
     *                 type="number",
     *                 format="decimal",
     *                 description="Pago por tarjeta",
     *                 nullable=true,
     *                 example=0.50
     *             ),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 description="Comentario",
     *                 nullable=true,
     *                 example="Pago parcial"
     *             ),

     *             @OA\Property(
     *                 property="person_id",
     *                 type="integer",
     *                 description="ID de persona",
     *                 nullable=true,
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="bank_id",
     *                 type="integer",
     *                 description="ID del banko",
     *                 nullable=true,
     *                 example=1
     *             ),

     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moviment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MovimentRequest")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Some fields are required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {

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

            'paymentConcept_id' => 'required|exists:concept_pays,id',
            'bank_id' => 'nullable|exists:banks,id',
            'person_id' => 'required|exists:people,id',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

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

        $tipo = 'M001';

        $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);

        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

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

        $efectivo = $request->input('cash') ?? 0;
        $yape = $request->input('yape') ?? 0;
        $plin = $request->input('plin') ?? 0;
        $tarjeta = $request->input('card') ?? 0;
        $deposito = $depositAmount ?? 0;

        $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

        $paymentConcetp = ConceptPay::find($request->input('paymentConcept_id'));

        $data = [

            'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentDate' => $request->input('paymentDate'),
            'total' => $total ?? 0,
            'yape' => $request->input('yape') ?? 0,
            'deposit' => $depositAmount ?? 0,
            'cash' => $request->input('cash') ?? 0,
            'card' => $request->input('card') ?? 0,
            'plin' => $request->input('plin') ?? 0,

            'isBankPayment' => $request->input('isBankPayment'),
            'routeVoucher' => $routeVoucher,
            'numberVoucher' => $numberVoucher,
            'typeDocument' => $paymentConcetp->type,

            'comment' => $request->input('comment') ?? '-',
            'status' => 'Generada',
            'paymentConcept_id' => $paymentConcetp->id,

            'person_id' => $request->input('person_id'),
            'user_id' => auth()->id(),
            'bank_id' => $bank_id,
        ];

        $object = Moviment::create($data);

        $image = $request->file('routeVoucher');

        if ($image) {
            $file = $image;
            $currentTime = now();
            $filename = $currentTime->format('YmdHis') . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/photosVouchers', $filename);
            $rutaImagen = Storage::url($path);
            $object->routeVoucher = $rutaImagen;
            $object->save();
        }

        $object = Moviment::with(['paymentConcept', 'user.worker.person'])->find($object->id);

        $object->detalle = $this->detalleCajaAperturada($movCaja->id, '', '')->original;

        return response()->json($object, 200);

    }
}
