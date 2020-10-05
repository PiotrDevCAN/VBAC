<?php
namespace vbac;

use vbac\personTable;
use itdq\Loader;

class personPortalLiteTable extends personTable
{
    
    
    function returnAsArray($preboadersAction=self::PORTAL_PRE_BOARDER_EXCLUDE){
        $data = array();
        
        $isFM   = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();
        
        
        $justaUser = !$_SESSION['isCdi']  && !$_SESSION['isPmo'] && !$_SESSION['isPes'] && !$_SESSION['isFm'] ;
        
        $predicate = " 1=1  ";
        
        $predicate .= $isFM ? " AND P.FM_CNUM='" . db2_escape_string(trim($myCnum)) . "' " : "";
        $predicate .= $justaUser ? " AND P.CNUM='" . db2_escape_string(trim($myCnum)) . "' " : ""; // FM Can only see their own people.
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_EXCLUDE ? " AND ( PES_STATUS_DETAILS not like 'Boarded as%' or PES_STATUS_DETAILS is null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_WITH_LINKED ? " AND ( PES_STATUS_DETAILS like 'Boarded as%' or PRE_BOARDED  is not  null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_ONLY_ACTIVE ? "  AND ( PES_STATUS_DETAILS not like 'Boarded as%' or PES_STATUS_DETAILS is null ) AND " . personTable::activePersonPredicate() : null;
        
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as P ";
        $sql.= " WHERE " . $predicate;
        
        $startOfSql = microtime(true);
        error_log("About to run SQL : $sql : " . $startOfSql);
        
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        $runSql = microtime(true);
        error_log("completed SQL" . $runSql . "(" . ($runSql-$startOfSql) . ") ");
        
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row=db2_fetch_assoc($rs))==true){
                // Only editable, if they're not a "pre-Boarder" who has now been boarded.
                $preparedRow = $this->prepareFields($row);
                $rowWithButtonsAdded =(substr($row['PES_STATUS_DETAILS'],0,7)=='Boarded') ? $preparedRow : $this->addButtons($preparedRow);
                $data[] = $rowWithButtonsAdded;                             
            }
        }
        
        $returnSql = microtime(true);
        error_log("About to return" . $returnSql . "(" . ($returnSql-$startOfSql) . ") ");
        
        
        return $data;        
        
    }
    
    function  prepareFields($row){
        $preparedRow = array_map('trim', $row);      
        return $preparedRow;
    }
    
    
    
}