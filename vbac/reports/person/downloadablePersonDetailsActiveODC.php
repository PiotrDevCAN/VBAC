<?php

namespace vbac\reports\person;

use vbac\allTables;
use vbac\personSquadRecord;
use vbac\personTable;
use vbac\reports\downloadablePerson;

class downloadablePersonDetailsActiveODC extends downloadablePerson
{    
    const TITLE = 'Aurora Person Table Extract(ODC) generated from vBAC';
    const SUBJECT = 'Person Table(ODC)';
    const DESCRIPTION = 'Aurora Person Table Extract(ODC) generated from vBAC';
    const PREFIX = 'personExtractActiveOdc';

    public function getReport($resultSetOnly = false)
    {
        $joins = " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$EMPLOYEE_AGILE_MAPPING . " AS EA ";
        $joins.= " ON P.CNUM = EA.CNUM AND P.WORKER_ID = EA.WORKER_ID AND EA.TYPE = '" . personSquadRecord::PRIMARY . "'";
        $joins.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS1 ";
        $joins.= " ON EA.SQUAD_NUMBER = AS1.SQUAD_NUMBER ";
        $joins.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
        $joins.= " ON AS1.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
        $joins.= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " AS SS ";
        $joins.= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
        
        $sql = " SELECT " . personTable::DEFAULT_SELECT_FIELDS .', ' . personTable::ORGANISATION_SELECT_ALL;
        $sql.= ", O.* ";
        $sql.= personTable::odcStaffSql($joins);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        if ($resultSetOnly) {
            return $rs;
        }

        $data = array();
        while (($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) == true) {
            //$report[] = array_map('trim', $row);
            $data[] = array_map('trim', $row);
        }

        return array('data' => $data, 'sql' => $sql);
    }
}