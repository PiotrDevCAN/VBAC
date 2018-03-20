<?php
namespace vbac;

use vbac\staticDataTable;
use itdq\DbTable;

class staticDataWorkstreamTable extends staticDataTable {


    function getallWorkstream(){
        $sql = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " ORDER BY WORKSTREAM ";
        $resultSet = db2_exec($_SESSION['conn'], $sql);

        $allWorkstream = null;
        if($resultSet){
            while (($row=db2_fetch_assoc($resultSet))==true) {
                $allWorkstream[trim($row['ACCOUNT_ORGANISATION'])][] = trim($row['WORKSTREAM']);
            }
        } else {
            DbTable::displayErrorMessage($resultSet,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return $allWorkstream;
    }
}