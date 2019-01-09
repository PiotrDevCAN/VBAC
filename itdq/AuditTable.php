<?php
namespace itdq;

use itdq\DbTable;
use itdq\AllItdqTables;

class AuditTable extends DbTable {
    const RECORD_TYPE_AUDIT = 'Audit';
    const RECORD_TYPE_DETAILS = 'Details';
    const RECORD_TYPE_REVALIDATION = 'Revalidation';


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



    static function returnAsArray($fromRecord=null, $length=null, $predicate=null){
        $fromRecord = !empty($fromRecord) ? $fromRecord : 1;
        $fromRecord = $fromRecord < 1 ? 1 : $fromRecord;
        $length = !empty($length) ? $length : 10;
        $length = $length < 1 ? 1 : $length;
        $end = $fromRecord + $length;
            
        $sql = " SELECT TIMESTAMP, EMAIL_ADDRESS, DATA, TYPE FROM ( ";
        $sql .= " SELECT ROW_NUMBER() OVER() AS rownum,A.* FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT . " AS A ";
        $sql .= " WHERE 1=1 ";
        $sql .= " AND TIMESTAMP >= (CURRENT TIMESTAMP - 31 days) ";
        $sql .= !empty($predicate)   ? "  $predicate " : null;
        $sql .= " ) as tmp ";
        $sql .= " WHERE ROWNUM >= $fromRecord AND ROWNUM < " .  $end ;
        $sql .= " ORDER BY TIMESTAMP DESC ";
     
        set_time_limit(0);
        
        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($row=db2_fetch_array($rs))==true){
            $trimmedRow = array_map('trim', $row);       
            $data[] = $trimmedRow;
        }
        set_time_limit(60);
        return $data;
     }
     
     static function recordsFiltered($predicate){
         $sql = " SELECT count(*) as recordsFiltered FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT . " AS A ";
         $sql .= " WHERE 1=1 ";
         $sql .= " AND TIMESTAMP >= (CURRENT TIMESTAMP - 31 days) ";
         $sql .= !empty($predicate)   ? "  $predicate " : null;
         
         $rs = db2_exec($_SESSION['conn'],$sql);
         
         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
         }
         
         $row=db2_fetch_assoc($rs);
         
         return $row['RECORDSFILTERED'];
         
         
     }
     
     static function totalRows(){
         $sql = " SELECT count(*) as totalRows FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$AUDIT . " AS A ";
         $rs = db2_exec($_SESSION['conn'],$sql);
        
         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
         }
         
         $row=db2_fetch_assoc($rs);   
        
         return $row['TOTALROWS'];
     }
     
}