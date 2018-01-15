<?php

use itdq\DbTable;
use itdq\AllItdqTables;

class AuditTable {


    static function audit($statement){
        if(property_exists('AllItdqTables','AUDIT')){
            $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . \itdq\AllItdqTables::$AUDIT;
            $sql . " ('TIMESTAMP','EMAIL_ADDRESS','DATA') ";
            $sql .= " VALUES ";
            $sql .= " ( CURRENT TIMESTAMP, '" . db2_escape_string($_SESSION['ssoEmail']) . "','" . db2_escape_string($statement) . "' ";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
        }
    }



    static function returnAsArray($fromTimestamp=null,$toTimestamp = null){
        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT;
        $sql .= " WHERE 1=1 ";
        $sql .= !empty($fromTimestamp) ? " AND 'TIMESTAMP'>= '" . db2_escape_string($fromTimestamp) . "' " : null;
        $sql .= !empty($toTimestamp)   ? " AND 'TIMESTAMP'<= '" . db2_escape_string($toTimestamp) . "' " : null;

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($row=db2_fetch_array($rs))==true){
            $data[] = $row;
        }

        return $data;

     }
}