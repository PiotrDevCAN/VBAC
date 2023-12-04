<?php

namespace vbac\reports;

use itdq\DbTable;
use vbac\interfaces\report;
use vbac\personTable;

class employeeComplete implements report
{
    public function getReport($resultSetOnly = false)
    {
        $withProvClear = null;

        $sql = " SELECT ";
        $sql.=" P.*, ";
        
        $sql.=personTable::EMPLOYEE_TYPE_SELECT.", ";

        // $sql.=" F.*, ";

        // $sql.=" U.*, ";

        // $sql.=" AS1.*, ";
        $sql.=" AS1.SQUAD_NUMBER, ";
        $sql.=" AS1.SQUAD_TYPE, ";
        $sql.=" AS1.TRIBE_NUMBER, ";
        $sql.=" AS1.SHIFT, ";
        $sql.=" AS1.SQUAD_LEADER, ";
        $sql.=" AS1.SQUAD_NAME, ";
        // $sql.=" AS1.ORGANISATION AS SQUAD_ORGANISATION, ";

        // $sql.=" AT.*, ";
        // $sql.=" AT.TRIBE_NUMBER, ";
        $sql.=" AT.TRIBE_NAME, ";
        $sql.=" AT.TRIBE_LEADER, ";
        // $sql.=" AT.ORGANISATION AS TRIBE_ORGANISATION, ";
        $sql.=" AT.ITERATION_MGR, ";

        $sql.=personTable::ORGANISATION_SELECT_ALL.", ";
        $sql.=personTable::FLM_SELECT.", ";
        $sql.=personTable::SLM_SELECT.", ";

        $sql.=" SS.*, ";
        
        $sql.= personTable::getStatusSelect($withProvClear, 'P');
        $sql.= personTable::getTablesForQuery();
        $sql.= " WHERE 1=1 AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
        $sql.= " ORDER BY P.KYN_EMAIL_ADDRESS ";

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