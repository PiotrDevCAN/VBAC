<?php

namespace vbac\reports\person;

use vbac\personRecord;
use vbac\personTable;
use vbac\reports\downloadablePerson;

class downloadablePersonBAU extends downloadablePerson
{    
    const TITLE = 'Aurora BAU Person Table Extract generated from vBAC';
    const SUBJECT = 'BAU Report';
    const DESCRIPTION = 'BAU report from Person Table Extract generated from vBAC';
    const PREFIX = 'personBauReport';

    public function getReport($resultSetOnly = false)
    {
        $activePredicate = personTable::activePersonPredicate(true, 'P');
        $sql = " SELECT " . personTable::DEFAULT_SELECT_FIELDS .', ' . personTable::ORGANISATION_SELECT_ALL;
        $sql.= personTable::getTablesForQuery();
        $sql.= " WHERE 1=1 AND " . $activePredicate;
        $sql.= " AND P.LOB     in ('GTS','Cloud','Security') ";
        $sql.= " AND P.TT_BAU  in ('BAU') ";
        $sql.= " AND P.CTB_RTB in ('CTB','RTB') ";
        $sql.= " AND trim(P.REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "' ";
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