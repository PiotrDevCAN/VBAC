<?php

use vbac\allTables;
use vbac\personTable;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$row = false;
$trimmedRow = false;

$withProvClear = !empty($_GET['withProvClear']) ? $_GET['withProvClear'] : null;

$sql = " SELECT DISTINCT ";
$sql.=" P.*, ";

$sql.=personTable::FULLNAME_SELECT.", ";
$sql.=personTable::EMPLOYEE_TYPE_SELECT.", ";

// $sql.=" F.*, ";

// $sql.=" U.*, ";

// $sql.=" AS1.*, ";
$sql.=" AS1.SQUAD_NUMBER, ";
$sql.=" AS1.SQUAD_TYPE, ";
// $sql.=" AS1.TRIBE_NUMBER, ";
$sql.=" AS1.SHIFT, ";
$sql.=" AS1.SQUAD_LEADER, ";
$sql.=" AS1.SQUAD_NAME, ";
// $sql.=" AS1.ORGANISATION AS SQUAD_ORGANISATION, ";

// $sql.=" AT.*, ";
$sql.=" AT.TRIBE_NUMBER, ";
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
$sql.= " WHERE 1=1 ";

error_log($sql);

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

$fields = array();
foreach( sqlsrv_field_metadata( $rs ) as $row ) {
    if(!in_array($row['Name'], $fields)) {
        $fields[] = $row['Name'];
    }
}

ob_clean();
header('Content-Type: application/json');
echo json_encode($fields);