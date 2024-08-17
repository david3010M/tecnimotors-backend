<?php

namespace App\Utils;

use App\Models\Attention;
use Carbon\Carbon;

class UtilFunctions
{
    public static function generateReportMovementClient($movements, $client, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_CAJA_CLIENTE_EXCEL);

        $excelUI->setTextCell("D4", $period);
        $excelUI->setTextCell("G4", $client);
        $col = $excelUI->getColumnIndex("A");
        $indexRow = 7;
        $index = 1;

        foreach ($movements as $movement) {
            $movement = json_decode($movement->toJson());
            $indexCol = $col;
            if ($indexRow % 2 == 0) {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
            } else {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
            }
            $excelUI->setRowHeight($indexRow, 30);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->numero);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->fecha);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->concepto);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->ingreso);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->egreso);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->presupuesto);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->metodo_pago);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->total);
            $indexRow++;
        }
        $excelUI->changeStyleSelected(true, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);

        $colTotalIncome = $excelUI->getColumnIndex("E");
        $colTotalExpense = $excelUI->getColumnIndex("F");
        $colTotal = $excelUI->getColumnIndex("H");

        $strColTotalIncome = $excelUI->getColumnStringFromIndex($colTotalIncome);
        $strColTotalExpense = $excelUI->getColumnStringFromIndex($colTotalExpense);

        $excelUI->setFormula($indexRow, $colTotalIncome, "=SUM({$strColTotalIncome}7:$strColTotalIncome" . ($indexRow - 1) . ")");
        $excelUI->setFormula($indexRow, $colTotalExpense, "=SUM({$strColTotalExpense}7:$strColTotalExpense" . ($indexRow - 1) . ")");

        $excelUI->setRowHeight($indexRow, 30);
        $excelUI->setDataCellByIndex($indexRow, $colTotal, "MONTO TOTAL");
        $colTotal++;
        $strCol = $excelUI->getColumnStringFromIndex($colTotal);
        $excelUI->setFormula($indexRow, $colTotal, "=SUM({$strCol}7:$strCol" . ($indexRow - 1) . ")");

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }

    public static function generateReportMovementVehicle($movements, $vehicle, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_CAJA_VEHICLE_EXCEL);

        $excelUI->setTextCell("D4", $period);
        $excelUI->setTextCell("G4", $vehicle);
        $col = $excelUI->getColumnIndex("A");
        $indexRow = 7;
        $index = 1;

        foreach ($movements as $movement) {
            $movement = json_decode($movement->toJson());
            $indexCol = $col;
            if ($indexRow % 2 == 0) {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
            } else {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
            }
            $excelUI->setRowHeight($indexRow, 30);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->numero);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->fecha);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->concepto);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->ingreso);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->egreso);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->presupuesto);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->metodo_pago);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->total);
            $indexRow++;
        }

        $excelUI->changeStyleSelected(true, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);

        $colTotalIncome = $excelUI->getColumnIndex("E");
        $colTotalExpense = $excelUI->getColumnIndex("F");
        $colTotal = $excelUI->getColumnIndex("H");

        $strColTotalIncome = $excelUI->getColumnStringFromIndex($colTotalIncome);
        $strColTotalExpense = $excelUI->getColumnStringFromIndex($colTotalExpense);

        $excelUI->setFormula($indexRow, $colTotalIncome, "=SUM({$strColTotalIncome}7:$strColTotalIncome" . ($indexRow - 1) . ")");
        $excelUI->setFormula($indexRow, $colTotalExpense, "=SUM({$strColTotalExpense}7:$strColTotalExpense" . ($indexRow - 1) . ")");

        $excelUI->setRowHeight($indexRow, 30);
        $excelUI->setDataCellByIndex($indexRow, $colTotal, "MONTO TOTAL");
        $colTotal++;
        $strCol = $excelUI->getColumnStringFromIndex($colTotal);
        $excelUI->setFormula($indexRow, $colTotal, "=SUM({$strCol}7:$strCol" . ($indexRow - 1) . ")");

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }

    public static function generateReportAttendanceVehicle($months, $period)
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_UNIDADES_ATENDIDAS);

        $excelUI->setTextCell("D3", $period);
        $sheetIndex = 0;

        foreach ($months as $month => $attentions) {
            $year = Carbon::parse($attentions->first()->arrivalDate)->format('Y');
            $indexClone = $excelUI->getIndexOfSheet("Base");
            $excelUI->cloneSheet($indexClone, $sheetIndex++, strtoupper($month) . ' ' . $year, true);
            $excelUI->setTextCell("A2", "UNIDADES ATENDIDAS DEL MES DE " . strtoupper($month) . " DEL AÑO $year");

            $col = $excelUI->getColumnIndex("A");
            $indexRow = 6;
            $index = 1;

            foreach ($attentions as $attention) {
                $attention = json_decode($attention->toJson());
                $indexCol = $col;
                if ($indexRow % 2 == 0) {
                    $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
                } else {
                    $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
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
