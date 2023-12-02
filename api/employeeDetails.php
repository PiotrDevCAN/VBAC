<?php

use vbac\allTables;
use vbac\personTable;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;

$withProvClear = !empty($_GET['withProvClear']) ? $_GET['withProvClear'] : null;

$row = false;
$trimmedRow = false;

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
$sql.= !empty($emailID) ? " AND (lower(P.EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "' OR lower(P.KYN_EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "') " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . htmlspecialchars(strtolower($notesId)) . "'  " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . htmlspecialchars(strtolower($cnum)) . "'  " : null;

error_log($sql);

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if($rs){
    $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
    $trimmedRow = array_map('trim', $row);
}

ob_clean();
header('Content-Type: application/json');
echo json_encode($trimmedRow);