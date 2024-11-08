<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceVehicleRequest;
use App\Http\Requests\MovementClientRequest;
use App\Http\Requests\MovementVehicleRequest;
use App\Http\Requests\ReportCommitmentRequest;
use App\Http\Requests\SaleProductReportRequest;
use App\Http\Requests\SaleReportRequest;
use App\Http\Resources\CommitmentResource;
use App\Http\Resources\ReportMovementClientResource;
use App\Http\Resources\ReportMovementDateRangeResource;
use App\Http\Resources\ReportNoteResource;
use App\Http\Resources\ReportSaleResource;
use App\Http\Resources\ServiceResource;
use App\Models\Attention;
use App\Models\Commitment;
use App\Models\Moviment;
use App\Models\Note;
use App\Models\Person;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Service;
use App\Utils\UtilFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExcelReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportMovementClient/{id}",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Movimientos de un Cliente",
     *     @OA\Parameter(name="id", in="path", description="ID del Cliente", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Reporte de Movimientos de un Cliente"),
     *     @OA\Response(response=404, description="Cliente no encontrado"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function reportMovementClient(MovementClientRequest $request, int $id)
    {
        $movements = Moviment::getMovementsByClientId($id, $request->from, $request->to);
        $movements = ReportMovementClientResource::collection($movements);
        $client = Person::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

//        return response()->json($movements);

        $clientName = $client->typeofDocument === 'RUC' ? $client->businessName : $client->names . ' ' . $client->fatherSurname . ' ' . $client->motherSurname;
        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateReportMovementClient($movements, $clientName, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Caja_Cliente_' . $id . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportAttendanceVehicle",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Atenciones de Vehículos",
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string"), example="2024-08-19"),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string"), example="2024-08-19"),
     *     @OA\Response(response=200, description="Reporte de Atenciones de Vehículos"),
     *     @OA\Response(response=404, description="Sin atenciones registradas", @OA\JsonContent(@OA\Property(property="message", type="string"))),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function reportAttendanceVehicle(AttendanceVehicleRequest $request)
    {
        $months = Attention::getAttentionByMonths($request->from, $request->to);
        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));
//
//        $countAttentionPerMonth = $months->map(function ($month) {
//            return $month->count();
//        });
//
//        return response()->json($months);
        if ($months->isEmpty()) {
            return response()->json([
                "message" => "No hay atenciones registradas en el rango de fechas proporcionado.",
            ], 404);
        }
        $bytes = UtilFunctions::generateReportAttendanceVehicle($months, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Unidades_Atendidas_' . $request->year . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportMovementVehicle",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Movimientos de un Vehículo",
     *     @OA\Parameter(name="plate", in="query", description="Placa del Vehículo", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Reporte de Movimientos de un Vehículo"),
     *     @OA\Response(response=404, description="Vehículo no encontrado"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function reportMovementVehicle(MovementVehicleRequest $request)
    {
        $movements = Moviment::getMovementsVehicle($request->plate, $request->from, $request->to);
        $movements = ReportMovementClientResource::collection($movements);

//        return response()->json($movements);

        $vehiclePlate = $request->plate;
        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateReportMovementVehicle($movements, $vehiclePlate, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Caja_Cliente_' . $vehiclePlate . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportSales",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Ventas",
     *     @OA\Parameter(name="number", in="query", description="Número de Venta", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="documentType", in="query", description="Tipo de Documento", required=false, @OA\Schema(type="string", enum={"BOLETA", "FACTURA", "TICKET"})),
     *     @OA\Parameter(name="saleType", in="query", description="Tipo de Venta", required=false, @OA\Schema(type="string", enum={"NORMAL", "DETRACCION"})),
     *     @OA\Parameter(name="paymentType", in="query", description="Tipo de Pago", required=false, @OA\Schema(type="string", enum={"CONTADO", "CREDITO"})),
     *     @OA\Parameter(name="status", in="query", description="Estado de la Venta", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="person_id", in="query", description="ID de la Persona", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="person$documentNumber", in="query", description="Número de Documento de la Persona", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="budget_sheet_id", in="query", description="ID de la Hoja de Presupuesto", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=404, description="Vehículo no encontrado", @OA\JsonContent(@OA\Property(property="message", type="string"))),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function reportSale(SaleReportRequest $request)
    {
        $sales = Sale::getSales(
            $request->input('number'),
            $request->input('from'),
            $request->input('to'),
            $request->input('documentType'),
            $request->input('saleType'),
            $request->input('paymentType'),
            $request->input('status'),
            $request->input('person_id'),
            $request->input('person$documentNumber'),
            $request->input('budget_sheet_id')
        );
        if ($sales->isEmpty()) {
            return response()->json([
                "message" => "No hay ventas registradas en el rango de fechas proporcionado.",
            ], 404);
        }
        $sales = ReportSaleResource::collection($sales);
//        return response()->json($sales);
        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateReportSales($sales, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Ventas' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportNotes",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Notas",
     *     @OA\Parameter(name="number", in="query", description="Número de Venta", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sale$number", in="query", description="Número de Venta", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sale$person_id", in="query", description="ID de la Persona", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=404, description="Vehículo no encontrado", @OA\JsonContent(@OA\Property(property="message", type="string"))),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function reportNote(SaleReportRequest $request)
    {
        $notes = Note::getNotes(
            $request->input('number'),
            $request->input('from'),
            $request->input('to'),
            $request->input('sale$number'),
            $request->input('sale$person_id')
        );
        if ($notes->isEmpty()) {
            return response()->json([
                "message" => "No hay notas registradas en el rango de fechas proporcionado.",
            ], 404);
        }
        $notes = ReportNoteResource::collection($notes);
//        return response()->json($notes);
        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateReportNotes($notes, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Notas' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportMovementDateRange/{id}",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Movimientos en un Rango de Fechas",
     *     description="Genera un reporte de movimientos de caja dentro de un rango de fechas, basado en un movimiento de apertura y opcionalmente un movimiento de cierre.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del movimiento de apertura",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reporte de Movimientos generado exitosamente",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimiento de Apertura no encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function reportMovementDateRange(MovementClientRequest $request, $id)
    {
        $movCajaAperturada = Moviment::where('id', $id)
            ->where('paymentConcept_id', 1)
            ->first();

        if (!$movCajaAperturada) {
            return response()->json([
                "message" => "Movimiento de Apertura no encontrado",
            ], 404);
        }

        // Buscar el movimiento de cierre
        $movCajaCierre = Moviment::where('id', '>', $movCajaAperturada->id)
            ->where('paymentConcept_id', 2)
            ->orderBy('id', 'asc')
            ->first();

        // Ajustar la consulta dependiendo de si existe o no un movimiento de cierre
        $movementsQuery = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
            ->where('id', '>=', $movCajaAperturada->id);

        if ($movCajaCierre) {
            $movementsQuery->where('id', '<', $movCajaCierre->id);
        }

        $movements = $movementsQuery->orderBy('id', 'desc')
            ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])
            ->get();

        $movements = ReportMovementDateRangeResource::collection($movements);

        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateReportMovementDateRange($movements, $movCajaAperturada->sequentialNumber, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Caja' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportServicios",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Servicios",
     *     description="Genera un reporte de todos los servicios registrados en el sistema dentro de un rango de fechas opcional.",
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Fecha de inicio",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Fecha de fin",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reporte de Servicios generado exitosamente",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function reportService(MovementClientRequest $request)
    {
        // Filtra por fechas si se proporcionan en la solicitud
        $query = Service::query();

        if ($request->from) {
            // Restar un día a la fecha de inicio
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->from)->subDay()->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }

        if ($request->to) {
            $toDate = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }

        // Obtén los movimientos filtrados
        $movements = $query->get();

        $movements = ServiceResource::collection($movements);

        $period = ($request->from && $request->to) ? 'Del ' . $fromDate->format('Y-m-d') . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $fromDate->format('Y-m-d') : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateService($movements, '', $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Servicios' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }


    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportCommitment",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Compromisos",
     *     description="Genera un reporte de compromisos basados en el estado y opcionalmente filtra por cliente.",
     *     @OA\Parameter(
     *         name="cliente_id",
     *         in="query",
     *         description="ID del cliente para filtrar los compromisos",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Estado del compromiso (por defecto: Pendiente)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Fecha de inicio",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Fecha de fin",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reporte de Compromisos generado exitosamente",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Persona no encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function reportCommitment(ReportCommitmentRequest $request)
    {
        $person_id = $request->cliente_id ?? null;

        $status = $request->status ?? 'Pendiente';
        $personNames = '';

        // Verificar si se ha proporcionado un ID de persona
        if (($person_id != null)) {

            $person = Person::find($person_id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada.'], 404);
            }

            // Obtener el nombre completo según el tipo de documento
            if ($person->typeofDocument === 'DNI') {
                $personNames = $person->names . ' ' . $person->fatherSurname;
            } elseif ($person->typeofDocument === 'RUC') {
                $personNames = $person->businessName;
            } else {
                $personNames = $person->names;
            }

        }

        // Crear una consulta base
        $query = Commitment::with('budgetSheet.attention.vehicle.person');

        // Aplicar filtro por cliente_id solo si se proporciona
        if (($person_id != null)) {
            $query->whereHas('budgetSheet.attention.vehicle.person', function ($q) use ($person_id) {
                $q->where('id', $person_id);
            });
        }

        // Aplicar filtro por estado
        $query->where('status', $status);

        // Ejecutar la consulta y transformar los resultados
        $movements = CommitmentResource::collection($query->get());

        // Determinar el periodo para el reporte
        $period = ($request->from && $request->to)
            ? 'Del ' . $request->from . ' al ' . $request->to
            : ($request->from
                ? 'Desde ' . $request->from
                : ($request->to
                    ? 'Hasta ' . $request->to
                    : '-'));

        // Generar el reporte en formato Excel
        $bytes = UtilFunctions::generateCommitment($movements, $period, $personNames, $status);
        $nameOfFile = date('d-m-Y') . '_Reporte_Compromisos' . '.xlsx';

        // Devolver el archivo generado como respuesta
        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }


    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportSaleProducts",
     *     tags={"Reporte Excel"},
     *     security={{"bearerAuth":{}}},
     *     summary="Reporte de Productos Vendidos",
     *     description="Genera un reporte de productos vendidos, filtrando por placa de vehículo, producto y rango de fechas.",
     *     @OA\Parameter( name="plate", in="query", description="Placa del vehículo", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter( name="product_id", in="query", description="ID del producto", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter( name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter( name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response( response=200, description="Reporte de Productos Vendidos generado exitosamente", @OA\MediaType( mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", @OA\Schema(type="string", format="binary"))),
     *     @OA\Response( response=401, description="Unauthorized"),
     *     @OA\Response( response=422, description="Validation error")
     * )
     */
    public function reportSaleProducts(SaleProductReportRequest $request)
    {
        $products = Product::getSaleProducts(
            $request->input('plate'),
            $request->input('product_id'),
            $request->input('from'),
            $request->input('to')
        );

        //return response()->json($products);

        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));
        $product = Product::find($request->product_id)->name ?? "-";
        $plate = $request->plate ?? "-";

        $bytes = UtilFunctions::generateReportSaleProducts($products, $product, $plate, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Productos_Vendidos' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

}
