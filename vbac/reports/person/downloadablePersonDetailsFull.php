<?php

namespace vbac\reports\person;

use vbac\personTable;
use vbac\reports\downloadablePerson;

class downloadablePersonDetailsFull extends downloadablePerson
{
    const SUBJECT = 'Full Person Table';
    const PREFIX = 'personExtractFull';
    
    public function getReport($resultSetOnly = false)
    {
        $excludePredicate = personTable::excludeBoardedPreboardersPredicate('P');
        $sql = " SELECT " . personTable::DEFAULT_SELECT_FIELDS .', ' . personTable::ORGANISATION_SELECT_ALL;
        $sql.= personTable::getTablesForQuery();
        $sql.= " WHERE 1=1 AND " . $excludePredicate;
    
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