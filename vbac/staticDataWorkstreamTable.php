<?php
namespace vbac;

use vbac\staticDataTable;
use itdq\DbTable;

class staticDataWorkstreamTable extends staticDataTable {

    function getallWorkstream(){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " ORDER BY WORKSTREAM ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        $allWorkstream = null;
        if($rs){
            while (($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true) {
                $allWorkstream[trim($row['ACCOUNT_ORGANISATION'])][] = trim($row['WORKSTREAM']);
            }
        } else {
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allWorkstream;
    }
}