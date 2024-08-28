<?php

namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelUI
{
    private $ss;
    private $ws;
    private $xDataFormat;
    /** @var Style $xStyleDefault */
    private $xStyleDefault;
    private $xStyleSelected;

    /** @var Style[] $listStyles */
    private $listStyles;

    public static $BACKGROUND_CELL_PRIMARY = 'C4E0F9';
    public static $BACKGROUND_CELL_SECONDARY = 'F0F7FE';
    public static $BACKGROUND_CELL_HEADER = '0F172A';
    public static $BACKGROUND_CELL_TOTAL = 'FFF2CC';

    public static $GENERAL = 'General';
    public static $FONT_DEFAULT = 'Segoe UI';
    public static $FORMAT_SOLES = '_-[$S/.-es_PE] * #,##0.00_-;_-[$S/.-es_PE] * -#,##0.00_-;_-[$S/.-es_PE] * "-"??_-;_-@_-';

    /* OK */
    public function __construct($module, $nameFile)
    {
        $template_path = Storage::path($module . DIRECTORY_SEPARATOR . $nameFile);
        if ($template_path === null) {
            throw new Exception("Plantilla de reporte no encontrada");
        }
        $input_file_type = IOFactory::identify($template_path);
        $reader = IOFactory::createReader($input_file_type);
        $this->ss = $reader->load($template_path);
        $this->ws = $this->ss->getActiveSheet();
        $this->initStyles();
    }

    /* OK */
    private function initStyles()
    {
        $this->listStyles = array();
        $this->xDataFormat = new NumberFormat();
        $this->xDataFormat->setFormatCode(self::$GENERAL);
        $this->xStyleDefault = new Style();
        $this->xStyleDefault->getAlignment()->setHorizontal('left');
        $this->xStyleDefault->getAlignment()->setVertical('center');
        $this->xStyleDefault->getNumberFormat()->setFormatCode($this->xDataFormat->getFormatCode());
        $font = $this->xStyleDefault->getFont();
        $font->setName(self::$FONT_DEFAULT);
        $font->setSize(11);
        $font->setBold(false);
        $this->xStyleDefault->setFont($font);
        $this->xStyleSelected = $this->xStyleDefault;
    }

    /* OK */
    public function setFontHeightStyleGeneral(float $fontHeight)
    {
        $this->xStyleDefault->getFont()->setSize($fontHeight);
    }

    public function setFontColorStyleGeneral(string $color)
    {
        $this->xStyleDefault->getFont()->setColor(new Color($color));
    }

    /* OK */
    public function changeStyleSelected(bool $bold, string $alignment = "L", string $dataFormat = null, bool $border, $color = null, bool $wrapText = false)
    {
        $dataFormatCode = 0;
        if (empty($dataFormat)) {
            $dataFormat = self::$GENERAL;
        }
        if (!is_null($color)) {
            $color = new Color($color);
        }
        $dataFormatCode = $this->xDataFormat->setFormatCode($dataFormat)->getFormatCode();
        $this->xStyleSelected = $this->generateStyle($bold, $alignment, $dataFormatCode, $border, $color, $wrapText);
    }

    private function getHorizontalAlignment($alignment)
    {
        $ha = Alignment::HORIZONTAL_LEFT;
        if ($alignment === null) {
            $alignment = "L";
        }
        if ($alignment === "C") {
            $ha = Alignment::HORIZONTAL_CENTER;
        } else if ($alignment === "R") {
            $ha = Alignment::HORIZONTAL_RIGHT;
        }
        return $ha;
    }

    public function getActiveSheet()
    {
        return $this->ws;
    }

    public function getCellCoordinates($row, $col)
    {
        return Coordinate::stringFromColumnIndex($col) . $row;
    }

    public function generateStyle(bool $bold, Alignment|string $alignment = null, string $dataFormatCode = null, $border, $color = null, $wrapText)
    {
        $style = null;
        if (gettype($alignment) === "string" || is_null($alignment))
            $alignment = $this->getHorizontalAlignment($alignment);
        if (is_null($dataFormatCode))
            $dataFormatCode = self::$GENERAL;
        $borderStyle = $border ? Border::BORDER_THIN : Border::BORDER_NONE;
        foreach ($this->listStyles as $item) {
            $currentColor = $item->getFont()->getColor();
            $sameColor = false;
            if ($currentColor === null && $color === null) {
                $sameColor = true;
            } else if ($currentColor !== null && $color !== null) {
                $sameColor = $currentColor->getRGB() == $color->getRGB();
            }
            if (
                $item->getFont()->getBold() == $bold
                && $item->getAlignment()->getHorizontal() == $alignment
                && $item->getNumberFormat()->getFormatCode() == $dataFormatCode && $item->getBorders()->getBottom()->getBorderStyle() == $borderStyle
                && $sameColor
                && $item->getAlignment()->getWrapText() == $wrapText
            ) {
                $style = $item;
                break;
            }
        }

        if ($style === null) {
            $style = clone $this->xStyleDefault;
            $newFont = $style->getFont();
            $newFont->setBold($bold);
            $newFont->setSize($this->xStyleDefault->getFont()->getSize());
            $style->setFont($newFont);
            $style->getAlignment()->setHorizontal($alignment);
            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $style->getNumberFormat()->setFormatCode($dataFormatCode);
            $style->getAlignment()->setWrapText($wrapText);
            if ($border) {
                if ($color === null) {
                    $borderColor = (new Color(self::$BACKGROUND_CELL_HEADER))->getRGB();
                } else {
                    $borderColor = $color->getRGB();
                }
                $borderStyle = Border::BORDER_THIN;
                $style->getBorders()->getBottom()->setBorderStyle($borderStyle)->getColor()->setRGB($borderColor);
                $style->getBorders()->getLeft()->setBorderStyle($borderStyle)->getColor()->setRGB($borderColor);
                $style->getBorders()->getRight()->setBorderStyle($borderStyle)->getColor()->setRGB($borderColor);
                $style->getBorders()->getTop()->setBorderStyle($borderStyle)->getColor()->setRGB($borderColor);
            }
            if ($color !== null) {
                // $style->getFont()->setColor($color);
                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color->getRGB());
            }
            $this->listStyles[] = $style;
        }
        return $style;
    }

    /* OK */
    public function setNamePrincipalSheet(string $nombre)
    {
        $this->ws->setTitle($nombre);
    }

    /* OK */
    public function setNameSheet(int $sheet, string $nombre)
    {
        $this->ss->getSheet($sheet)->setTitle($nombre);
    }

    /* OK */
    public function changeSheet(int $sheet)
    {
        $this->ws = $this->ss->getSheet($sheet);
    }

    /* OK */
    public function cloneSheet(int $cloneSheet, int $newSheet, string $nameSheet, bool $activeSheet)
    {
        // Verificar si el nombre ya existe en el libro
        $existingSheetNames = [];
        foreach ($this->ss->getSheetNames() as $sheetName) {
            $existingSheetNames[] = $sheetName;
        }

        // Si el nombre ya existe, generar un nombre único agregando un sufijo numérico
        $originalName = $nameSheet;
        $suffix = 1;
        while (in_array($nameSheet, $existingSheetNames)) {
            $nameSheet = $originalName . ' (' . $suffix++ . ')';
        }

        if ($this->ss->getSheetCount() < $newSheet) {
            $this->ss->createSheet($newSheet);
        }

        $clonedSheet = clone $this->ss->getSheet($cloneSheet);
        $clonedSheet->setTitle($nameSheet);  // Establecer el nombre único
        $this->ss->addSheet($clonedSheet, $newSheet);

        if ($activeSheet) {
            $this->ws = $this->ss->getSheet($newSheet);
        }
    }

    public function getIndexOfSheet(string $nameSheet)
    {
        return $this->ss->getIndex($this->ss->getSheetByName($nameSheet));
    }

    public function deleteSheet(int $sheet)
    {
        $this->ss->removeSheetByIndex($sheet);
    }

    public function setActiveSheetIndex(int $sheet)
    {
        $this->ss->setActiveSheetIndex($sheet);
        $this->ws = $this->ss->getActiveSheet();
    }


    /* OK */
    public function setColumnWidth(string|int $col, $width)
    {
        if (gettype($col) == "string") {
            $this->ws->getColumnDimension($col)->setWidth($width);
        } else if (gettype($col) == "integer") {
            $this->ws->getColumnDimensionByColumn($col)->setWidth($width);
        } else {
            throw new Exception("Invalid type of column");
        }
    }

    public function setRowHeight(int $row, float $height)
    {
        $this->ws->getRowDimension($row)->setRowHeight($height);
    }

    /* OK */
    public function setColumnStyle(string|int $col, string $alignment = null, string $dataFormat)
    {
        if (gettype($col) == "string")
            $col = Coordinate::columnIndexFromString($col);
        $format = ($dataFormat == null) ? '0' : $this->xDataFormat->setFormatCode($dataFormat);
        $style = $this->generateStyle(false, $alignment, $format, false, null, false);
        $this->ws->getStyle($col)->applyFromArray($style->exportArray());
    }

    /* OK */
    private function generateCell(int $rowIndex, int $colIndex): Cell
    {
        //$cellAddress = Coordinate::stringFromColumnIndex($colIndex) . $rowIndex;
        // $cell = $this->ws->getCell([$colIndex, $rowIndex]);
        return $this->ws->getCell([$colIndex, $rowIndex]);
    }

    /* OK */
    public function setColumnFormat(int $colIndex, string $format)
    {
        $this->generateCell(0, $colIndex)->getStyle()->getNumberFormat()->setFormatCode($format);
    }

    /* OK */
    private function setDataCellFinal(int $row, int $col, $value)
    {
        $cell = $this->generateCell($row, $col);

        if (is_null($value) || $value === "" || ctype_space(trim($value))) {
            // echo 'nada';
            // dump($value, is_null($value), $value === "", preg_match('/^\s$/', $value));
        } else if (is_string($value)) {
            // echo 'string';
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        } else if (is_int($value) || is_float($value) || is_numeric($value) || is_double($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
        } else {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        }
        return $cell;
    }

    /* OK */
    private function setDataCellFinalStyle(int $row, int $col, $val, bool $numeric_as_money = false)
    {
        if ($numeric_as_money) {
            $xStyleSelected = clone $this->xStyleSelected;
            $xStyleSelected->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $xStyleSelected->getNumberFormat()->setFormatCode(self::$FORMAT_SOLES);
            $this->setDataCellFinal($row, $col, $val)->getStyle()->applyFromArray($xStyleSelected->exportArray());
            unset($xStyleSelected);
        } else {
            $this->setDataCellFinal($row, $col, $val)->getStyle()->applyFromArray($this->xStyleSelected->exportArray());
        }
    }

    /* OK */
    private function setDataCellFinalStyleTwo(int $row, int $col, $val, bool $bold, string $alignment = null)
    {
        $this->xStyleSelected->getFont()->setBold($bold);
        if ($alignment !== null) {
            $this->xStyleSelected->getAlignment()->setHorizontal($alignment);
        }
        $this->setDataCellFinal($row, $col, $val)->getStyle()
            ->applyFromArray($this->xStyleSelected->exportArray());
    }

    /* OK */
    public function setDataCell(int $row, string $colName, $val, bool $bold = false, string $alignment = null)
    {
        // $row--;
        $col = Coordinate::columnIndexFromString($colName);
        $this->setDataCellFinalStyleTwo($row, $col, $val, $bold, $alignment);

    }

    /* OK */
    public function setDataCellByIndex(int $row, int $col, $val, bool $numeric_as_money = false)
    {
        // $row--;
        $this->setDataCellFinalStyle($row, $col, $val, $numeric_as_money);

    }

    public function getColumnIndex(string $colName)
    {
        return Coordinate::columnIndexFromString($colName);
    }

    /* OK */
    public function setDataCellString(string $cell, $value, bool $bold = false, string $alignment = null)
    {
        [$col, $row] = Coordinate::coordinateFromString($cell);
        $this->setDataCellFinalStyleTwo($row, Coordinate::columnIndexFromString($col), $value, $bold, $alignment);
    }

    public function setTextCell(string $cell, string $text)
    {
        $this->ws->setCellValue($cell, $text);
    }

    /* OK */
    public function setDataRange(string $cellIni, array $data)
    {
        if (empty($data))
            return;
        // if (!preg_match('/^[A-Z]+\d+:[A-Z]+\d+$/', $range)) return;
        // [$celIni, $cellFin]= Coordinate::rangeBoundaries($range);
        $celIni = Coordinate::coordinateFromString($cellIni);
        $colIndexIni = Coordinate::columnIndexFromString($celIni[0]);
        $celFin = [$colIndexIni + count($data[0]), $celIni[1] + count($data) - 1];
        $range = Coordinate::stringFromColumnIndex($colIndexIni) . $celIni[1] . ":" . Coordinate::stringFromColumnIndex($celFin[0]) . $celFin[1];
        $this->ws->fromArray($data, null, $cellIni);
        $this->ws->getStyle($range)->applyFromArray($this->xStyleSelected->exportArray());
    }

    /* OK */
    public function setCellBorder(string $range)
    {
        if (!preg_match('/^[A-Z]+\d+:[A-Z]+\d+$/', $range))
            return;
        $style = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF6D60'],
                ],
            ]
        ];
        $this->ws->getStyle($range)->applyFromArray($style);
    }

    /* OK */
    public function setCellBorderColor(string $range, string $color)
    {
        if (!preg_match('/^[A-Z]+\d+:[A-Z]+\d+$/', $range))
            return;
        $style = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
            ]
        ];
        $this->ws->getStyle($range)->applyFromArray($style);
    }

    /* OK */
    public function setCellfontBold(string $range)
    {
        if (!preg_match('/^[A-Z]+\d+:[A-Z]+\d+$/', $range))
            return;
        $style = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $this->ws->getStyle($range)->applyFromArray($style);
    }


    public function mergeCells(int $rowIni, int $rowFin, string $colNameIni, string $colNameFin)
    {
        $rowIni--;
        $rowFin--;
        $colIni = Coordinate::columnIndexFromString($colNameIni);
        $colFin = Coordinate::columnIndexFromString($colNameFin);
        for ($i = $rowIni; $i <= $rowFin; $i++) {
            $this->ws->getRowDimension($i)->setVisible(true);
            for ($j = $colIni; $j <= $colFin; $j++) {
                $this->ws->getCell(Coordinate::stringFromColumnIndex($j) . $i);
            }
        }
        $range = Coordinate::stringFromColumnIndex($colIni) . strval($rowIni + 1) . ":" . Coordinate::stringFromColumnIndex($colFin) . strval($rowFin + 1);
        $this->ws->mergeCells($range);
    }

    public function setFormula(int $row, string|int $colName, string $formula)
    {
        if (gettype($colName) == 'string')
            $colName = Coordinate::columnIndexFromString($colName);
        $this->generateCell($row, $colName)->setValueExplicit($formula, DataType::TYPE_FORMULA)->getStyle()->applyFromArray($this->xStyleSelected->exportArray());
    }

    public function getColumnIndexFromString(string $colName)
    {
        return Coordinate::columnIndexFromString($colName);
    }

    public function getColumnStringFromIndex(int $colIndex)
    {
        return Coordinate::stringFromColumnIndex($colIndex);
    }

    public function setWrapText($range)
    {
        if (!preg_match('/^[A-Z]+\d+:[A-Z]+\d+$/', $range))
            return;
        $this->ws->getStyle($range)->getAlignment()->setWrapText(true);
    }

    public function getColumnWidth($columName)
    {
        return $this->ws->getColumnDimension($columName)->getWidth();
    }

    public function save()
    {
        $writer = new Xlsx($this->ss);
        $uniqueId = uniqid();
        $tmpFilePath = sys_get_temp_dir() . '/' . $uniqueId . '_excel.xlsx';
        $writer->save($tmpFilePath);
        $bytes = file_get_contents($tmpFilePath);
        unlink($tmpFilePath);
        return $bytes;
    }
}
