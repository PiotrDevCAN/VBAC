<?php
namespace vbac;

use vbac\personTable;
use itdq\DbTable;

class personPortalLiteTable extends personTable
{
    function __construct($table,$pwd=null,$log=true){
        parent::__construct($table,$pwd,$log);
        unset($this->columns['FCNUM']); // Ignore this column, only there for the predicate for Func Mgrs.        
    }
    
    function returnAsArray($preboadersAction = self::PORTAL_PRE_BOARDER_EXCLUDE, $start = 0, $length = 10, $predicate = null, $sorting = null){
        
        // $sql = self::preparePersonStmt($start, $length, $preBoardersPredicate, $predicate, $sorting);

        $this->allDelegates = delegateTable::allDelegates();
        
        $data = array();
        
        $isFM = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();
        
        $justaUser = !$_SESSION['isCdi']  && !$_SESSION['isPmo'] && !$_SESSION['isPes'] && !$_SESSION['isFm'] ;
        
        $predicate = " 1=1  ";
        $withProvClear = true;
        
        $predicate .= $isFM ? " AND P.FM_CNUM='" . db2_escape_string(trim($myCnum)) . "' " : "";
        $predicate .= $justaUser ? " AND P.CNUM='" . db2_escape_string(trim($myCnum)) . "' " : ""; // FM Can only see their own people.
        
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_EXCLUDE ? " AND ( P.PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or P.PES_STATUS_DETAILS is null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_PRE_BOARDER_WITH_LINKED ? " AND ( P.PES_STATUS_DETAILS like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or P.PRE_BOARDED  is not  null) " : null;
        $predicate .= $preboadersAction==self::PORTAL_ONLY_ACTIVE ? "  AND ( P.PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or P.PES_STATUS_DETAILS is null ) AND " . personTable::activePersonPredicate($withProvClear, 'P') : null;
        
        $sql = " SELECT 
        P.CNUM, 
        P.OPEN_SEAT_NUMBER, 
        P.FIRST_NAME, 
        P.LAST_NAME, 
        P.EMAIL_ADDRESS, 
        P.KYN_EMAIL_ADDRESS, 
        P.NOTES_ID, 
        P.LBG_EMAIL, 
        CASE WHEN EM.DESCRIPTION IS NOT NULL THEN EM.DESCRIPTION ELSE INITCAP(P.EMPLOYEE_TYPE) END AS EMPLOYEE_TYPE,
        CASE WHEN F.KYN_EMAIL_ADDRESS IS NOT NULL THEN F.KYN_EMAIL_ADDRESS ELSE F.CNUM END as FM_CNUM,
        P.FM_CNUM as FCNUM,
        P.FM_MANAGER_FLAG, 
        SS.SKILLSET, 
        P.PROPOSED_LEAVING_DATE,
        P.LOB, 
        P.START_DATE, 
        P.PROJECTED_END_DATE, 
        P.COUNTRY, 
        P.IBM_BASE_LOCATION, 
        P.LBG_LOCATION, 
        P.PES_DATE_REQUESTED, 
        P.PES_REQUESTOR, 
        P.PES_DATE_RESPONDED, 
        P.PES_STATUS_DETAILS, 
        P.PES_STATUS, 
        P.REVALIDATION_DATE_FIELD, 
        P.REVALIDATION_STATUS, 
        P.CBN_DATE_FIELD, 
        P.CBN_STATUS, 
        P.CT_ID, 
        P.PRE_BOARDED, 
        P.PES_DATE_EVIDENCE, 
        P.RSA_TOKEN, 
        P.CALLSIGN_ID, 
        P.PES_LEVEL, 
        P.PES_RECHECK_DATE, 
        P.PES_CLEARED_DATE, 
        P.SQUAD_NUMBER, 
        P.PMO_STATUS,
        PT.PROCESSING_STATUS ,
        PT.PROCESSING_STATUS_CHANGED, 
        AS.SQUAD_NAME,
        AS.SQUAD_LEADER, 
        AT.TRIBE_NUMBER,
        AT.TRIBE_NAME,
        AT.TRIBE_LEADER,
        AT.ITERATION_MGR,";
        $sql .= self::ORGANISATION_SELECT;
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " AS PT ";
        $sql.= " ON P.CNUM = PT.CNUM ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
        $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
        $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
        $sql.= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " as SS ";
        $sql.= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS F ";
        $sql.= " ON P.FM_CNUM = F.CNUM ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$EMPLOYEE_TYPE_MAPPING . " AS EM ";
        $sql.= " ON P.EMPLOYEE_TYPE = EM.CODE ";
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
 
        return array('data'=>$data,'sql'=>$sql);
    }
    
    function prepareFields($row){
        unset($row['FCNUM']);
        $preparedRow = array_map('trim', $row);   
        $preparedRow['fmCnum'] = $row['FM_CNUM'];
        $preparedRow['OLD_SQUAD_NUMBER'] = 'none';
        return $preparedRow;
    }

    function headerRowForFullDatatable(){
        $personPortalColumns = array(
            'CNUM', 
            'OPEN_SEAT_NUMBER', 
            'FIRST_NAME', 
            'LAST_NAME', 
            'EMAIL_ADDRESS', 
            'KYN_EMAIL_ADDRESS',
            'NOTES_ID', 
            'LBG_EMAIL', 
            'EMPLOYEE_TYPE', 
            'FM_CNUM', 
            'FM_MANAGER_FLAG', 
            'LOB',
            'SKILLSET',
            'START_DATE', 
            'PROJECTED_END_DATE', 
            'COUNTRY', 
            'IBM_BASE_LOCATION', 
            'LBG_LOCATION', 
            'PES_DATE_REQUESTED', 
            'PES_REQUESTOR', 
            'PES_DATE_RESPONDED', 
            'PES_STATUS_DETAILS', 
            'PES_STATUS', 
            'REVALIDATION_DATE_FIELD', 
            'REVALIDATION_STATUS', 
            'PROPOSED_LEAVING_DATE',
            'CBN_DATE_FIELD', 
            'CBN_STATUS', 
            'CT_ID', 
            'PRE_BOARDED', 
            'PES_DATE_EVIDENCE', 
            'RSA_TOKEN', 
            'CALLSIGN_ID', 
            'PROCESSING_STATUS', 
            'PROCESSING_STATUS_CHANGED', 
            'PES_LEVEL', 
            'PES_RECHECK_DATE', 
            'PES_CLEARED_DATE', 
            'SQUAD_NUMBER', 
            'SQUAD_NAME', 
            'SQUAD_LEADER', 
            'TRIBE_NUMBER', 
            'TRIBE_NAME', 
            'TRIBE_LEADER', 
            'ORGANISATION', 
            'ITERATION_MGR', 
            'FCNUM', 
            'PMO_STATUS'
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName ) . "</th>";
        }
        $headerRow.= "</tr>";
        return $headerRow;
    }
}