<?php
namespace vbac;

use itdq\DbTable;
use itdq\DbRecord;


class odcAssetRemovalTable extends DbTable {
    
    
    function odcPopulationWithRemoveByPlatform(){
        
        $personTable = new personTable(allTables::$PERSON);        
        $activePredicate = $personTable->activePersonPredicate();        
        
        $sql = " SELECT upper(trim(WORK_STREAM)) as WORK_STREAM, COUNT(*) as Platform_Population_With_Remove ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as O ";
        $sql.= " ON O.CNUM = P.CNUM ";
        $sql.= " WHERE 1=1 ";
        $sql.= " and " . $activePredicate;
        $sql.= " and TT_BAU='BAU' ";
        $sql.= " AND WORK_STREAM is not null ";
        $sql.= " AND O.CNUM is not null ";        
        $sql.= " GROUP BY WORK_STREAM ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            DbTable::displayErrorMessage($rs, '', '', $sql);
        }
        
        $totalPopulation=0;
        $platformPopulation = array();
        
        while(($row=db2_fetch_assoc($rs))==true){
            $platformPopulation[strtoupper(trim($row['WORK_STREAM']))] = $row['PLATFORM_POPULATION_WITH_REMOVE']+0;
            $totalPopulation += $row['PLATFORM_POPULATION_WITH_REMOVE'];
        }
        
        return array('TotalPopulationWithRemove'=>$totalPopulation,'PlatformPopulationsWithRemove'=>$platformPopulation,'sql'=>$sql);
    } 
    
    
    function rightToRemove(){
        $sql = "SELECT P.NOTES_ID, P.FIRST_NAME, P.LAST_NAME, O.CNUM, O.ASSET_SERIAL_NUMBER, O.START_DATE, O.END_DATE, P.WORK_STREAM ";
        $sql.= "from " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as O ";
        $sql.= "left join " . $_SESSION['Db2Schema'] . "." . \vbac\allTables::$PERSON . " as P ";
        $sql.= "on O.CNUM = P.CNUM ";
        $sql.= " ORDER BY NOTES_ID ";
        

        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }
        
    
    
}
    