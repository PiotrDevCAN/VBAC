<?php
namespace vbac;

use itdq\DbTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class personWithSubPTable extends personTable {

    protected $personSubPlatform;

    function __construct($table,$pwd=null, $log=true){
        parent::__construct($table,$pwd,$log);

        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON_SUBPLATFORM;
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        while(($subPlatformRecord=sqlsrv_fetch_array($rs))==true){
            $subPlatformRecord = array_map('trim', $subPlatformRecord);
            $this->personSubPlatform[$subPlatformRecord['CNUM']][] = $subPlatformRecord['SUBPLATFORM'];
        }

    }


    function addButtons($row){
        $row = parent::addButtons($row);
        $row['SUBPLATFORM'] = !empty($this->personSubPlatform[$row['CNUM']]) ? implode(',',$this->personSubPlatform[$row['CNUM']]) :  '';
        return $row;
    }


    function addSubplatform($row){
        $row['SUBPLATFORM'] = !empty($this->personSubPlatform[$row['CNUM']]) ? implode(',',$this->personSubPlatform[$row['CNUM']]) :  '';
        return $row;
    }

    static function writeResultSetToXls( $resultSet,Spreadsheet $spreadsheet,$withColumnHeadings=true,$columnIndex=1,$rowIndex=1){
        $personSubPlatform = array();
        $originalConnection = $GLOBALS['conn'];

        include("connect.php");

        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON_SUBPLATFORM;
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        while(($subPlatformRecord=sqlsrv_fetch_array($rs))==true){
            $subPlatformRecord = array_map('trim', $subPlatformRecord);
            $personSubPlatform[$subPlatformRecord['CNUM']][] = $subPlatformRecord['SUBPLATFORM'];
        }

        $GLOBALS['conn'] = $originalConnection;

        $rowsWritten = false;
        $headerRow=true;
        $columnCounter = $columnIndex;
        $rowCounter = $rowIndex;

        while (($rawRow=sqlsrv_fetch_array($resultSet))==true) {
            $rowsWritten = true;
            $row = array_map('trim', $rawRow);
            $row = static::preProcessRowForWriteToXls($row);
            $row['SUBPLATFORM'] = !empty($personSubPlatform[$row['CNUM']]) ? implode(',',$personSubPlatform[$row['CNUM']]) :  '';
            if($headerRow && $withColumnHeadings){
                foreach ($row as $columnName => $value){
                    $columnHeading = ucwords(str_replace("_", " ", $columnName));
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($columnCounter++, $rowCounter, $columnHeading);
                }
                $headerRow = false;
                $rowCounter++;
                $columnCounter=$columnIndex;
            }
            foreach ($row as $columnName => $value){
                $strValue = " " . $value . " ";
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($columnCounter++, $rowCounter, $strValue);
            }
            $rowCounter++;
            $columnCounter=$columnIndex;
        }
        return $rowsWritten;
    }





}