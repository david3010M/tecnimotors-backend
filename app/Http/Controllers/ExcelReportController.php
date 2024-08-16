<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceVehicleRequest;
use App\Http\Requests\MovementClientRequest;
use App\Http\Resources\ReportMovementClientResource;
use App\Models\Attention;
use App\Models\Moviment;
use App\Models\Person;
use App\Utils\UtilFunctions;

class ExcelReportController extends Controller
{
    public function reportMovementClient(MovementClientRequest $request, int $id)
    {
        $movements = Moviment::getMovementsByClientId($id, $request->from, $request->to);
        $movements = ReportMovementClientResource::collection($movements);
        $client = Person::find($id);
        if (!$client) return response()->json(['message' => 'Client not found'], 404);

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
}
