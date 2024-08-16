<?php

namespace App\Utils;

use App\Http\Resources\ReportAttendanceVehicleResource;
use App\Models\Attention;
use Carbon\Carbon;

class UtilFunctions
{
    public static function generateReportMovementeClient($movements)
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_CAJA_CLIENTE_EXCEL);
        $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, null, true);
        $excelUI->setDataCellString("D6", $contrato->numero ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("D7", $estado ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("D8", $tipo ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("D9", $entidad_financiera->nombre ?? Constants::NOT_REQUESTED_TEXT);

        $excelUI->setDataCellString("G6", $fecha_inicio ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("G7", $fecha_termino ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("G8", $fecha_vigencia ?? Constants::NOT_REQUESTED_TEXT);
        $excelUI->setDataCellString("G9", $proveedor->razon_denom ?? Constants::NOT_REQUESTED_TEXT);

        $col = $excelUI->getColumnIndex("B");
        $indexRow = 14;
        $index = 1;

        foreach ($movements as $movement) {
            $indexCol = $col;
            if ($indexRow % 2 == 0) {
                $excelUI->changeStyleSelected(false, "L", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
            } else {
                $excelUI->changeStyleSelected(false, "L", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
            }
            $excelUI->setRowHeight($indexRow, 30);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, Carbon::parse(1)->format(Constants::FORMAT_DATE_ORACLE));
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, Carbon::parse(1)->format(Constants::FORMAT_DATE_ORACLE));
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, Carbon::parse(1)->format(Constants::FORMAT_DATE_ORACLE));
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, 1);
            $indexRow++;
        }
//        $excelUI->setRowHeight($indexRow, 30);
//        $excelUI->changeStyleSelected(true, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);
//        $excelUI->setDataCellByIndex($indexRow, $indexCol -= 2, "MONTO TOTAL", true);
//
//        $excelUI->setRowHeight($indexRow, 30);
//        $excelUI->changeStyleSelected(true, "C", ExcelUI::$FORMAT_SOLES, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);
//        $indexCol++;
//        $strCol = $excelUI->getColumnStringFromIndex($indexCol);
//        $excelUI->setFormula($indexRow, $indexCol, "=SUM({$strCol}13:$strCol" . ($indexRow - 1) . ")");

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }

    public static function generateReportAttendanceVehicle(int $year)
    {
        $months = Attention::getAttentionByMonths($year);

        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_UNIDADES_ATENDIDAS);
        $sheetIndex = 0;

        foreach ($months as $month => $attentions) {
            $indexClone = $excelUI->getIndexOfSheet("Base");
            $excelUI->cloneSheet($indexClone, $sheetIndex++, $month, true);
            $excelUI->setTextCell("A2", "UNIDADES ATENDIDAS DEL MES DE " . strtoupper($month) . " DEL AÑO $year");

            $col = $excelUI->getColumnIndex("A");
            $indexRow = 5;
            $index = 1;

            foreach ($attentions as $attention) {
                $attention = json_decode($attention->toJson());
                $indexCol = $col;
                if ($indexRow % 2 == 0) {
                    $excelUI->changeStyleSelected(false, "L", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
                } else {
                    $excelUI->changeStyleSelected(false, "L", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
                }

                $excelUI->setRowHeight($indexRow, 30);
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++); // N°
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->fecha); // FECHA
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->numero); // NUMERO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->cliente); // CLIENTE
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->marca); // MARCA
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->modelo); // MODELO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->placa); // PLACA
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->kilometraje); // KILOMETRAJE
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->anio); // AÑO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->servicio); // SERVICIO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->responsable); // RESPONSABLE
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->recepcion); // RECEPCION
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->metodo); // METODO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->pago); // PAGO
                $excelUI->setDataCellByIndex($indexRow, $indexCol++, $attention->debe); // DEBE

                $indexRow++;
            }
        }

        $excelUI->deleteSheet($excelUI->getIndexOfSheet("Base"));
        $excelUI->setActiveSheetIndex(0);

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }


}
