<?php

namespace App\Utils;

use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Style\Font;

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

    public static function generateReportSales($sales, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_VENTA_EXCEL);

        $excelUI->setTextCell("C4", $period);
        $col = $excelUI->getColumnIndex("A");
        $indexRow = 7;
        $index = 1;

        foreach ($sales as $sale) {
            $movement = json_decode($sale->toJson());
            $indexCol = $col;
            if ($indexRow % 2 == 0) {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
            } else {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
            }
            $excelUI->setRowHeight($indexRow, 30);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->correlativo);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->fecha);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->tipoDocumento);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->tipoPago);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->cliente);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->estado);
//            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->metodoPago);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->total);
            $indexRow++;
        }

        $excelUI->changeStyleSelected(true, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);

        $colTotal = $excelUI->getColumnIndex("G");
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

    public static function generateReportMovementDateRange($movements, $client, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_CAJA_EXCEL);

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

    public static function generateService($movements, $client, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_SERVICIOS_EXCEL);

        $col = $excelUI->getColumnIndex("D");
        $indexRow = 6;
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
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->name);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->saleprice);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, Carbon::parse($movement->created_at)->format('d-m-Y H:i:s'));
            $indexRow++;
        }

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }

    public static function generateCommitment($movements, $period = "-", $personNames = '-', $status = 'Pendiente')
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_COMPROMISOS);

        $excelUI->setTextCell("C4", $period);
        $excelUI->setTextCell("F4", $personNames);
        $excelUI->setTextCell("I4", $status);

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
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->number);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->client);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->payment_type);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, Carbon::parse($movement->payment_date)->format('d-m-Y H:i:s'));

            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->price);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->balance);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->amount_paid);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $movement->status);

            $indexRow++;
        }

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }

    public static function generateReportSaleProducts($products, $product, $plate, $period = "-")
    {
        $excelUI = new ExcelUI(Constants::REPORTES, Constants::REPORTE_PRODUCTOS_VENDIDOS);

        $excelUI->setTextCell("C4", $period);
        $excelUI->setTextCell("G4", $plate);
        $excelUI->setTextCell("J4", $product);
        $col = $excelUI->getColumnIndex("A");
        $indexRow = 7;
        $index = 1;

        foreach ($products as $product) {
            $product = json_decode($product->toJson());
            $indexCol = $col;
            if ($indexRow % 2 == 0) {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_PRIMARY, true);
            } else {
                $excelUI->changeStyleSelected(false, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_SECONDARY, true);
            }
            $excelUI->setRowHeight($indexRow, 30);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $index++);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->name);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->date);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, (float)$product->purchase_price);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, (float)$product->sale_price);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, (int)$product->quantity);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, (float)$product->total);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, (int)$product->stock);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->type);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->category);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->unit);
            $excelUI->setDataCellByIndex($indexRow, $indexCol++, $product->brand);
            if (!empty($product->attention_id)) {
                $attentionUrl = "https://develop.garzasoft.com/tecnimotors-backend/public/ordenservicio/{$product->attention_id}";
                $attentionLink = new Hyperlink($attentionUrl, 'Ver detalle');
                $coordinates = $excelUI->getCellCoordinates($indexRow, $indexCol++);
                $excelUI->getActiveSheet()->setCellValue($coordinates, 'Ver detalle');
                $excelUI->getActiveSheet()->getCell($coordinates)->setHyperlink($attentionLink);

                $excelUI->getActiveSheet()->getStyle($coordinates)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0563C1'],
                        'underline' => Font::UNDERLINE_SINGLE,
                    ],
                    'alignment' => ['horizontal' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F0F7FE'],
                    ],
                ]);
            }

            if (!empty($product->budgetSheet_id)) {
                $budgetSheetUrl = "https://develop.garzasoft.com/tecnimotors-backend/public/presupuesto/{$product->budgetSheet_id}";
                $budgetSheetLink = new Hyperlink($budgetSheetUrl, 'Ver detalle');
                $coordinates = $excelUI->getCellCoordinates($indexRow, $indexCol++);
                $excelUI->getActiveSheet()->setCellValue($coordinates, 'Ver detalle');
                $excelUI->getActiveSheet()->getCell($coordinates)->setHyperlink($budgetSheetLink);

                $excelUI->getActiveSheet()->getStyle($coordinates)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0563C1'],
                        'underline' => Font::UNDERLINE_SINGLE,
                    ],
                    'alignment' => ['horizontal' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F0F7FE'],
                    ],
                ]);
            }

            $indexRow++;
        }
        $excelUI->changeStyleSelected(true, "C", ExcelUI::$GENERAL, true, ExcelUI::$BACKGROUND_CELL_TOTAL, true);

        $colTotal = $excelUI->getColumnIndex("F");

        $excelUI->setRowHeight($indexRow, 30);
        $excelUI->setDataCellByIndex($indexRow, $colTotal, "MONTO TOTAL");
        $colTotal++;
        $strCol = $excelUI->getColumnStringFromIndex($colTotal);
        $excelUI->setFormula($indexRow, $colTotal, "=SUM({$strCol}7:$strCol" . ($indexRow - 1) . ")");

        $bytes = $excelUI->save();
        unset($excelUI);
        return $bytes;
    }


}
