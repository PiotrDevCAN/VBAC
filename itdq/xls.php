<?php
namespace itdq;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

trait xls{

    static function writeResultSetToXls($resultSet, Spreadsheet $spreadsheet, $withColumnHeadings=true, $columnIndex=1, $rowIndex=1) {

        $rowsWritten = false;
        $headerRow=true;
        $columnCounter = $columnIndex;
        $rowCounter = $rowIndex;

        while ($rawRow = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
            $rowsWritten = true;
            $row = array_map('trim', $rawRow);
            $row = static::preProcessRowForWriteToXls($row);
            if ($headerRow && $withColumnHeadings){
                foreach ($row as $columnName => $value){
                    $columnHeading = ucwords(str_replace("_", " ", $columnName));
                    $spreadsheet
                        ->getActiveSheet()
                        ->setCellValueByColumnAndRow($columnCounter, $rowCounter, $columnHeading);
                    $columnCounter++;
                }
                $headerRow = false;
                $rowCounter++;
            }

            $columnCounter = $columnIndex;
            foreach ($row as $columnName => $value){
                $strValue = strval($value);
                $spreadsheet
                    ->getActiveSheet()
                    ->setCellValueByColumnAndRow($columnCounter, $rowCounter, $strValue);
                $columnCounter++;
            }
            $rowCounter++;
        }
        return $rowsWritten;
    }
    
    static function preProcessRowForWriteToXls($row){
        return $row;
    }

    static function autoSizeColumns(Spreadsheet $spreadsheet){
        $sheet = $spreadsheet->getActiveSheet();
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        /** @var PHPExcel_Cell $cell */
        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }
    }

    static function autoFilter(Spreadsheet $spreadsheet){
        $spreadsheet->getActiveSheet()->setAutoFilter(
            $spreadsheet->getActiveSheet()
                ->calculateWorksheetDimension()
        );
    }

    static function setRowColor(Spreadsheet $spreadsheet, $color='80333333', $rowIndex=1){
        $sheet = $spreadsheet->getActiveSheet();
        $cellIterator = $sheet->getRowIterator($rowIndex)->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            $cell
                ->getStyle()
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($color);
        }
    }

    static function setColumnFormatting(Spreadsheet $spreadsheet, $columnIndex=1, $rowIndex=1) {
        $sheet = $spreadsheet->getActiveSheet();
        $columnIterator = $sheet->getColumnIterator();
        foreach ($columnIterator as $column) {
            $cellIterator = $column->getCellIterator();
            $updateColumn = false;
            foreach ($cellIterator as $key => $cell) {
                if ($key == 1) {
                    $columnName = $cell->getValue();
                    if (endsWith(strtolower($columnName), 'date')) {
                        $updateColumn = true;
                    }
                } else {
                    if ($updateColumn) {
                        $cell->getStyle()
                            ->getNumberFormat()
                            ->setFormatCode(
                                \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD 
                            );
                    }
                }
            }
        }
    }
}