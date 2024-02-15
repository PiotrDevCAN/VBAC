<?php
namespace vbac;

use itdq\DbTable;

class cFIRSTPersonTable extends DbTable{

    function returnAsArray(){
        
        $data = array();
        
        $predicate = " 1=1  ";
        
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CP ";        
        $sql.= " WHERE " . $predicate;
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){                         
                $data[] = $row;
            }
        }
 
        return array('data'=>$data,'sql'=>$sql);
    }

    function headerRowForDatatable(){
        $headerRow = "<tr>";
        foreach ($this->columns as $columnName => $db2ColumnProperties) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName );
        }
        return $headerRow;
    }
}