<?php
namespace itdq;

use itdq\DbTable;
use itdq\AllItdqTables;

class AuditTable extends DbTable {
    const RECORD_TYPE_AUDIT = 'Audit';
    const RECORD_TYPE_DETAILS = 'Details';


    static function audit($statement,$type='Details'){
        if(property_exists('itdq\AllItdqTables','AUDIT')){
            $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . \itdq\AllItdqTables::$AUDIT;
            $sql . " ('TIMESTAMP','EMAIL_ADDRESS','DATA','TYPE') ";
            $sql .= " VALUES ";
            $sql .= " ( CURRENT TIMESTAMP, '" . db2_escape_string($_SESSION['ssoEmail']) . "','" . db2_escape_string($statement) . "','" . db2_escape_string($type) . "' )";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
        }
    }

    static function removeExpired($auditLifeSpan=null,$detailsLifeSpan=null){
        if(property_exists('itdq\AllItdqTables','AUDIT')){
            $auditLifeSpan = empty($auditLifeSpan) ? $_SESSION['AuditLife'] : $auditLifeSpan;
            $detailsLifeSpan = empty($detailsLifeSpan) ? $_SESSION['AuditDetailsLife'] : $detailsLifeSpan;

            $sql  = " DELETE FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT ;
            $sql .= " WHERE " ;
            $sql .= " (TYPE='" . self::RECORD_TYPE_AUDIT . "' AND \"TIMESTAMP\" < ( CURRENT TIMESTAMP - " . db2_escape_string($auditLifeSpan) . " )) ";
            $sql .= " OR " ;
            $sql .= " (TYPE='" . self::RECORD_TYPE_DETAILS . "' AND \"TIMESTAMP\" < ( CURRENT TIMESTAMP - " . db2_escape_string($detailsLifeSpan) . " ))  ";

            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }

            return true;
        }
    }



    static function returnAsArray($fromTimestamp=null,$toTimestamp = null, $predicate=null){
        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT;
        $sql .= " WHERE 1=1 ";
        $sql .= !empty($fromTimestamp) ? " AND 'TIMESTAMP'>= '" . db2_escape_string($fromTimestamp) . "' " : null;
        $sql .= !empty($toTimestamp)   ? " AND 'TIMESTAMP'<= '" . db2_escape_string($toTimestamp) . "' " : null;
        $sql .= !empty($predicate)   ? " AND $predicate " : null;

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