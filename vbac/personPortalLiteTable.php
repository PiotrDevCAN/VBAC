<?php
namespace vbac;

use vbac\personTable;
use itdq\Loader;

class personPortalLiteTable extends personTable
{
    function __construct($table,$pwd=null,$log=true){
        parent::__construct($table,$pwd,$log);
        unset($this->columns['FCNUM']); // Ignore this column, only there for the predicate for Func Mgrs.        
    }
    
    
    function returnAsArray($preboadersAction=self::PORTAL_PRE_BOARDER_EXCLUDE){
        
        $this->allDelegates = delegateTable::allDelegates();
        
        $data = array();
        
        $isFM   = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();
        
        
        $justaUser = !$_SESSION['isCdi']  && !$_SESSION['isPmo'] && !$_SESSION['isPes'] && !$_SESSION['isFm'] ;
        
        $predicate = " 1=1  ";
        
        $predicate .= $isFM ? " AND P.FCNUM='" . db2_escape_string(trim($myCnum)) . "' " : "";
        $predicate .= $justaUser ? " AND P.CNUM='" . db2_escape_string(trim($myCnum)) . "' " : ""; // FM Can only see their own people.
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_EXCLUDE ? " AND ( PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PES_STATUS_DETAILS is null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_WITH_LINKED ? " AND ( PES_STATUS_DETAILS like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PRE_BOARDED  is not  null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_ONLY_ACTIVE ? "  AND ( PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PES_STATUS_DETAILS is null ) AND " . personTable::activePersonPredicate() : null;
        
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
                $rowWithButtonsAdded =(substr($row['PES_STATUS_DETAILS'],0,10)==personRecord::PES_STATUS_DETAILS_BOARDED_AS) ? $preparedRow : $this->addButtons($preparedRow);
                $data[] = $rowWithButtonsAdded;                             
            }
        }
        
        $returnSql = microtime(true);
        error_log("About to return" . $returnSql . "(" . ($returnSql-$startOfSql) . ") ");
        
        
        return $data;        
        
    }
    
    function  prepareFields($row){
        unset($row['FCNUM']);
        $preparedRow = array_map('trim', $row);   
        return $preparedRow;
    }
    
    
    
}