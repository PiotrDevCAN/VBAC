<?php
namespace vbac;

use itdq\DbTable;


class odcAssetRemovalTable extends DbTable {
    
    
    function odcPopulationWithRemoveByPlatform(){
        
        $personTable = new personTable(allTables::$PERSON);        
        $activePredicate = $personTable->activePersonPredicate();        
        
        $sql = " SELECT upper(trim(WORK_STREAM)) as WORK_STREAM, COUNT(distinct O.CNUM) as Platform_Population_With_Remove ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as O ";
        $sql.= " ON O.CNUM = P.CNUM ";
        $sql.= " WHERE 1=1 ";
        $sql.= " and " . $activePredicate;
        $sql.= " and TT_BAU='BAU' ";
        $sql.= " AND WORK_STREAM is not null ";
        $sql.= " AND O.CNUM is not null ";        
        $sql.= " GROUP BY WORK_STREAM ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            echo print_r(sqlsrv_errors());
            echo print_r(sqlsrv_errors());
            DbTable::displayErrorMessage($rs, '', '', $sql);
        }
        
        $totalPopulation=0;
        $platformPopulation = array();
        
        while(($row = sqlsrv_fetch_array($rs))==true){
            $platformPopulation[strtoupper(trim($row['WORK_STREAM']))] = $row['PLATFORM_POPULATION_WITH_REMOVE']+0;
            $totalPopulation += $row['PLATFORM_POPULATION_WITH_REMOVE'];
        }
        
        return array('TotalPopulationWithRemove'=>$totalPopulation,'PlatformPopulationsWithRemove'=>$platformPopulation,'sql'=>$sql);
    } 
    
    
    function rightToRemove(){
        $sql = "SELECT P.NOTES_ID, P.FIRST_NAME, P.LAST_NAME, O.CNUM, O.ASSET_SERIAL_NUMBER, O.START_DATE, O.END_DATE, P.WORK_STREAM ";
        $sql.= "from " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as O ";
        $sql.= "left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= "on O.CNUM = P.CNUM ";
        $sql.= " ORDER BY NOTES_ID ";
        

        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }
        
    
    
}
    