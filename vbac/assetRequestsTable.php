<?php
namespace vbac;

use itdq\DbTable;
use itdq\DbRecord;


class assetRequestsTable extends DbTable{
    
    private static $portalHeaderCells = array('REFERENCE','CT_ID','PERSON','ASSET','STATUS','JUSTIFICATION','REQUESTOR','APPROVER',
        'LOCATION','PRIMARY_UID','SECONDARY_UID','DATE_ISSUED_TO_IBM','DATE_ISSUED_TO_USER','DATE_RETURNED',
        'EDUCATION_CONFIRMED','ORDERIT_GROUP_REF','ORDERIT_NUMBER','ORDERIT_STATUS','ORDERIT_TYPE');
    

//     function saveRecord(assetRequestRecord $record, $populatedColumns, $nullColumns, $commit){
//         parent::saveRecord($record, $populatedColumns, $nullColumns, $commit);
//     }
    
    static function portalHeaderCells(){
        $headerCells = null;
       // $widths = array(5,5,5,5,10,10,10,10,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
         foreach (self::$portalHeaderCells as $key => $value) {
//                 $width = 'width="' . $widths[$key] . '"';
                $headerCells .= "<th>";
                $headerCells .= str_replace("_", " ", $value);
                $headerCells .= "</th>";
         }
         return $headerCells;
    }

    static function returnForPortal($predicate=null){
        $sql  = " SELECT ";
//        $sql .= " concat('000000',AR.REQUEST_REFERNCE) as car,";
        $sql .= " AR.REQUEST_REFERENCE as reference, ";
        $sql .= " P.CONTRACTOR_ID as CT_ID, P.EMAIL_ADDRESS, P.NOTES_ID, AR.ASSET_TITLE as ASSET, STATUS, ";
        $sql .= " BUSINESS_JUSTIFICATION as JUSTIFICATION, REQUESTOR_EMAIL as REQUESTOR_EMAIL, REQUESTED as REQUESTED_DATE,  ";
        $sql .= " APPROVER_EMAIL, APPROVED as APPROVED_DATE, ";
        $sql .= " USER_LOCATION as LOCATION, ";
        $sql .= " PRIMARY_UID, SECONDARY_UID, DATE_ISSUED_TO_IBM, DATE_ISSUED_TO_USER, DATE_RETURNED, EDUCATION_CONFIRMED ";
        $sql .= " ORDERIT_GROUP_REF, ORDERIT_NUMBER, ORDERIT_STATUS, ";
        $sql .= " RAL.ORDER_IT_TYPE as ORDERIT_TYPE ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";      
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON TRIM(RAL.ASSET_TITLE) = TRIM(AR.ASSET_TITLE) ";
        $sql .= " WHERE 1=1 ";
        $sql .= $predicate;  

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($row=db2_fetch_assoc($rs))==true){
            $row['PERSON'] = $row['NOTES_ID'];
            unset($row['EMAIL_ADDRESS']);
            unset($row['NOTES_ID']);
                       
            $row['APPROVER'] = $row['APPROVER_EMAIL'] . "<br/><small>" . $row['APPROVED_DATE'] . "</small>";
            unset($row['APPROVER_EMAIL']);
            unset($row['APPROVED_DATE']);
            
            $row['REQUESTOR'] = $row['REQUESTOR_EMAIL'] . "<br/><small>" . $row['REQUESTED_DATE'] . "</small>";
            unset($row['REQUESTOR_EMAIL']);
            unset($row['REQUESTOR_DATE']);
            
            $data[] = $row;
        }

        return $data;

    }
    
    private function getNextVarb(){
        $sql  = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . allTables::$ORDER_IT_VARB_TRACKER;
        $sql .= " ( CREATED_BY ) VALUES ('" . db2_escape_string($GLOBALS['ltcuser']['mail']) . "' )" ;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $varbRef = db2_last_insert_id($_SESSION ['conn']);
        
        $nextVarb = 'vARB' . substr('000000' . $varbRef ,-5);
        return $nextVarb;      
    }
    
    
    function getRequestsForOrderIt($orderItGroup, $tempFile){    
        
//        $tempFile = tmpfile();
//         fwrite($temp, "writing to tempfile");
        
        
        $nextVarb = $this->getNextVarb();   
        
        $commitState  = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);
        
        $sql =  "UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql .= " SET ORDERIT_VARB_REF = '$nextVarb', ORDERIT_STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " WHERE REQUEST_REFERENCE in ";
        $sql .= " (SELECT REQUEST_REFERENCE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= "   WHERE ORDERIT_VARB_REF is null and RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItGroup) . "' AND AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $sql .= "   AND ('" . db2_escape_string($orderItType) . "' == 1 or P.CONTRACTOR_ID is not null)";
        $sql .= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql .= "   FETCH FIRST 20 ROWS ONLY) ";
    
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
       
        $sql = " SELECT ORDERIT_VARB_REF, REQUEST_REFERENCE, ";
        $sql .= " P.CONTRACTOR_ID as CT_ID, ";
        $sql .= " ASSET_TITLE, ";
        $sql .= " CASE when P.NOTES_ID is null then P.EMAIL_ADDRESS else P.NOTES_ID end as IDENTITY, ";
        $sql .= " case when BUSINESS_JUSTIFICATION is null then 'N/A' else BUSINESS_JUSTIFICATION end as JUSTIFICATION, ";
        $sql .= " STATUS,  USER_LOCATION, REQUESTOR_EMAIL, REQUESTED,  APPROVER_EMAIL, APPROVED, current timestamp as EXPORTED ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";      
        $sql .= " WHERE ORDERIT_VARB_REF = '" . $nextVarb . "' ";        
        $sql .= " ORDER BY ASSET_TITLE, REQUEST_REFERENCE desc";
  
        $data = array();
        $data[] = '"VARB","REQUEST","CT ID","ASSET TITLE","EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","EXPORTED"';
        
        $rs2 = db2_exec($_SESSION['conn'],$sql);    
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        while(($row=db2_fetch_assoc($rs2))==true){    
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }
        
        
        foreach ($data as $request){
            fwrite($tempFile, $request. "\n\r");
        }
        
        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$commitState);
 
        return $tempFile;
    }
    
    
    function exportForOrderIT($orderItGroup = 0){
        $rows = $this->getRequestsForOrderIt($orderItGroup);
        return $rows;
    }
    
    
    function countApprovedForOrderItType($orderItType = 0){
        $sql  = " SELECT COUNT(*) as REQUESTS ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM "; 
        $sql .= " WHERE ORDERIT_VARB_REF is null and RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItType) . "' AND AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $sql .= " AND ('" . db2_escape_string($orderItType) . "' != 1 or P.CONTRACTOR_ID is not null)"
        
        $rs = db2_exec($_SESSION['conn'],$sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = db2_fetch_assoc($rs);
        return $row['REQUESTS'];
        
    }

}