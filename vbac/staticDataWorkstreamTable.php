<?php
namespace vbac;

use vbac\staticDataTable;
use itdq\DbTable;

class staticDataWorkstreamTable extends staticDataTable {

    function getallWorkstream(){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " ORDER BY WORKSTREAM ";
        $rs = db2_exec($GLOBALS['conn'], $sql);

        $allWorkstream = null;
        if($rs){
            while (($row=db2_fetch_assoc($rs))==true) {
                $allWorkstream[trim($row['ACCOUNT_ORGANISATION'])][] = trim($row['WORKSTREAM']);
            }
        } else {
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allWorkstream;
    }
}