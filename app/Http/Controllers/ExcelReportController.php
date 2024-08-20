<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceVehicleRequest;
use App\Http\Requests\MovementClientRequest;
use App\Http\Requests\MovementVehicleRequest;
use App\Http\Requests\ReportCommitmentRequest;
use App\Http\Resources\CommitmentResource;
use App\Http\Resources\ReportMovementClientResource;
use App\Http\Resources\ReportMovementDateRangeResource;
use App\Http\Resources\ServiceResource;
use App\Models\Attention;
use App\Models\Commitment;
use App\Models\Moviment;
use App\Models\Person;
use App\Models\Service;
use App\Utils\UtilFunctions;
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
        $movements = Service::get();

        $movements = ServiceResource::collection($movements);

//        return response()->json($movements);

        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
            ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

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

}
