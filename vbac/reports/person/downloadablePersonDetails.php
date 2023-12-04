<?php

namespace vbac\reports\person;

use vbac\allTables;
use vbac\reports\downloadablePerson;

class downloadablePersonDetails extends downloadablePerson
{
    const PREFIX = 'personExtract';
    
    public function getReport($resultSetOnly = false)
    {
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;

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