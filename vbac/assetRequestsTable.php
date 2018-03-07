<?php
namespace vbac;

use itdq\DbTable;
use itdq\DbRecord;


class assetRequestsTable extends DbTable{
    
    private static $portalHeaderCells = array('REFERENCE','CT_ID','PERSON','ASSET','STATUS','JUSTIFICATION','REQUESTOR','APPROVER',
        'LOCATION','PRIMARY_UID','SECONDARY_UID','DATE_ISSUED_TO_IBM','DATE_ISSUED_TO_USER','DATE_RETURNED',
        'EDUCATION_CONFIRMED','ORDERIT_GROUP_REF','ORDERIT_NUMBER','ORDERIT_STATUS','ORDERIT_CLASS');
    

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
        $sql .= " RAL.ORDER_IT_GROUP as ORDERIT_CLASS ";
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
    
    
    function getRowsForOrderIt($orderItGroup){        
        $nextVarb = $this->getNextVarb();   
        
        $commitState  = db2_autocommit($_SESSION['conn']);
        
        $sql =  "UPDATE ";
        $sql .= " (SELECT REQUEST_REFERENCE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= "   WHERE ORDERIT_VARB_REF is null and RAL.ORDER_IT_GROUP = '" . db2_escape_string($orderItGroup) . "' ";
        $sql .= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql .= "   FETCH FIRST 20 ROWS ONLY) ";
        $sql .= " SET ORDERIT_VARB_REF = '$nextVarb' ";
        
                
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        db2_commit($_SESSION['conn']);
        
        $sql = " SELECT * ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " WHERE ORDER_VBAC_REF = '" . $nextVarb . "' ";
        
        $data = array();
        
        while(($row=db2_fetch_assoc($rs))==true){            
            $data[] = $row;
        }
        
        return $data;
    }
    
    
    function exportForOrderIT($orderItGroup = 0){
        $rows = $this->getRowsForOrderIt($orderItGroup);
        return $rows;
    }

}