<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceVehicleRequest;
use App\Http\Requests\CommitmentRequest;
use App\Http\Requests\MovementClientRequest;
use App\Http\Requests\MovementVehicleRequest;
use App\Http\Requests\ReportCommitmentRequest;
use App\Http\Requests\ServiceRequest;
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
    public function reportMovementClient(MovementClientRequest $request, int $id)
    {
        $movements = Moviment::getMovementsByClientId($id, $request->from, $request->to);
        $movements = ReportMovementClientResource::collection($movements);
        $client = Person::find($id);
        if (!$client) return response()->json(['message' => 'Client not found'], 404);

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
        $bytes = UtilFunctions::generateReportAttendanceVehicle($months, $period);
        $nameOfFile = date('d-m-Y') . '_Reporte_Unidades_Atendidas_' . $request->year . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }

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

    public function reportMovementDateRange(MovementClientRequest $request, $id)
    {
        $movements = Moviment::getMovementsByDateRange($request->from, $request->to);

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

        $movements = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
            ->where('id', '>=', $movCajaAperturada->id)

            ->where('id', '<', $movCajaCierre->id)
            ->orderBy('id', 'desc')
            ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])
            ->get();

        $movements = ReportMovementDateRangeResource::collection($movements);

//        return response()->json($movements);

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

    public function reportCommitment(ReportCommitmentRequest $request)
    {
     
        $person_id = $request->cliente_id ?? null;
        $status = $request->status ?? 'Pendiente';
        $personNames = '';
        if ($person_id) {
            $person = Person::find($person_id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada.'], 404);
            }

            $typeOfDocument = $person->typeOfDocument; 
            if ($typeOfDocument === 'DNI') {
                $fullName = $person->names . ' ' . $person->fatherSurname;
            } elseif ($typeOfDocument === 'RUC') {
                $fullName = $person->businessName;
            } else {
                $fullName = $person->names; 
            }

            $personNames = $fullName;
        }

        // Crear una consulta base
        $query = Commitment::with('budgetSheet.attention.vehicle.person');

        // Aplicar filtro por cliente_id si se proporciona
        if ($person_id) {
            $query->whereHas('budgetSheet.attention.vehicle.person', function ($q) use ($person_id) {
                $q->where('id', $person_id);
            });
        }

        $query->where('status', $status);

        // Ejecutar la consulta
        $movements = $query->get();
        $movements = CommitmentResource::collection($movements);

//        return response()->json($movements);

        $period = ($request->from && $request->to) ? 'Del ' . $request->from . ' al ' . $request->to :
        ($request->from ? 'Desde ' . $request->from : ($request->to ? 'Hasta ' . $request->to : '-'));

        $bytes = UtilFunctions::generateCommitment($movements, $period,  $personNames, $status);
        $nameOfFile = date('d-m-Y') . '_Reporte_Compromisos' . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }
}
