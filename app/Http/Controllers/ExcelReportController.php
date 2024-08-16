<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceVehicleRequest;
use App\Models\Moviment;
use App\Utils\UtilFunctions;
use Illuminate\Http\Request;

class ExcelReportController extends Controller
{
    public function reportMovementClient(int $id)
    {
        $movements = Moviment::with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])
            ->where('person_id', $id)
            ->get();

        $bytes = UtilFunctions::generateReportMovementeClient($movements);

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="reporte_movimiento_cliente.xlsx"',
            'Content-Length' => strlen($bytes),
        ]);
    }

    public function reportAttendanceVehicle(AttendanceVehicleRequest $request)
    {
//        $months = Attention::getAttentionByMonths($request->year);
//
//        $countAttentionPerMonth = $months->map(function ($month) {
//            return $month->count();
//        });
//
//        return response()->json($countAttentionPerMonth);

        $bytes = UtilFunctions::generateReportAttendanceVehicle($request->year);
        $nameOfFile = date('d-m-Y') . '_Unidades_Atendidas_' . $request->year . '.xlsx';

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nameOfFile . '"',
            'Content-Length' => strlen($bytes),
        ]);
    }
}
