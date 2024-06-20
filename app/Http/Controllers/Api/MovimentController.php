<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Moviment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimentController extends Controller
{
    /**
     * Get all Movimentes
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/moviment",
     *     tags={"Moviment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="box_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="ID of the box to filter the Movimentes"
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
        $movCaja = Moviment::where('status', 'Activa')
            ->where('paymentConcept_id', 1)
            ->first();
        $data = [];
        if ($movCaja) {
            // $data = $this->detalleCajaAperturada($movCaja->id)->original;
            $data = [];
        }
        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/transporte/public/api/movimentAperturaCierre",
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
     *                 property="box_id",
     *                 type="integer",
     *                 description="ID de la caja",
     *                 nullable=true,
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="branchOffice_id",
     *                 type="integer",
     *                 description="ID de la sucursal",
     *                 nullable=true,
     *                 example=1
     *             ),

     *             @OA\Property(
     *                 property="person_id",
     *                 type="integer",
     *                 description="ID de la persona",
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
            'yape' => 'required|numeric',
            'deposit' => 'required|numeric',
            'cash' => 'required|numeric',
            'plin' => 'required|numeric',
            'card' => 'required|numeric',
            'comment' => 'nullable|string',
            'isBankPayment' => 'requeride|in:0,1',

            'paymentConcept_id' => 'required|in:1,2|exists:concept_pays,id',
            'bank_id' => 'nullable|exists:banks,id',
            'person_id' => 'required|exists:people,id',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $branch_office_id = $request->input('branchOffice_id');

        if ($request->input('paymentConcept_id') == 1) {
            $letra = 'A';
            $status = 'Activa';

        } else if (($request->input('paymentConcept_id') == 2)) {
            $letra = 'C';
            $status = 'Inactiva';

            $movCaja = Moviment::where('status', 'Activa')
                ->where('paymentConcept_id', 1)
                ->first();

            $movCaja->status = 'Inactiva';
            $movCaja->save();

        }

        $tipo = $letra . str_pad($branch_office_id, 3, '0', STR_PAD_LEFT);

        $tipo = str_pad($tipo, 4, '0', STR_PAD_RIGHT);

        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM moviments WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

        $efectivo = $request->input('cash') ?? 0;
        $yape = $request->input('yape') ?? 0;
        $plin = $request->input('plin') ?? 0;
        $tarjeta = $request->input('card') ?? 0;
        $deposito = $request->input('deposit') ?? 0;

        $total = $efectivo + $yape + $plin + $tarjeta + $deposito;

        $routeVoucher=null;
        $numberVoucher=null;
        $bank_id=null;

        if ($request->input('isBankPayment') == 1) {

        }

        $data = [

            'sequentialNumber' => $tipo . '-' . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'paymentDate' => $request->input('paymentDate'),
            'total' => $total ?? 0,
            'yape' => $request->input('yape') ?? 0,
            'deposit' => $request->input('deposit') ?? 0,
            'cash' => $request->input('cash') ?? 0,
            'card' => $request->input('card') ?? 0,
            'plin' => $request->input('plin') ?? 0,

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

        $object = Moviment::with(['bank', 'paymentConcept',
            'person', 'user.worker.person'])->find($object->id);
        return response()->json($object, 200);

    }
}
