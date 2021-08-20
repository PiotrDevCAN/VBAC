<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$onlyActiveStr = !empty($_GET['onlyactive']) ? $_GET['onlyactive'] : 'true';
$onlyActiveBool = $onlyActiveStr=='true';
$withProvClear = !empty($_GET['withProvClear']) ? $_GET['withProvClear'] : null;

$translatedPlus = str_replace(
    array(
        'JRSS',
        'FLL_CNUM',
        'FLL_NOTES_ID',
        'SLL_CNUM',
        'SLL_NOTES_ID'
    ),
    array(
        'P.ROLE_ON_THE_ACCOUNT',
        'F.CNUM as FLL_CNUM',
        'F.NOTES_ID as FLL_NOTES_ID',
        'U.CNUM as SLL_CNUM',
        'U.NOTES_ID as SLL_NOTES_ID'
    ),
    strtoupper($_REQUEST['plus'])
);

$additionalFields = !empty($_REQUEST['plus']) ? explode(",",$translatedPlus) : null;
$additionalSelect = null;
$employees = array();

foreach ($additionalFields as $field) {
    $additionalSelect .= ", " . db2_escape_string($field);
}

$sql = " SELECT distinct P.NOTES_ID, AS.SQUAD_NUMBER, T.TRIBE_NUMBER " . $additionalSelect;
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
$sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$AGILE_TRIBE . " AS T ";
$sql.= " ON AS.TRIBE_NUMBER = T.TRIBE_NUMBER ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$PERSON . " AS F ";
$sql.= " ON P.FM_CNUM = F.CNUM ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$PERSON . " AS U ";
$sql.= " ON F.FM_CNUM = U.CNUM ";
$sql.= " WHERE 1=1 AND trim(P.NOTES_ID) != '' ";
$sql.= $onlyActiveBool ? " AND " . personTable::activePersonPredicate($withProvClear,'P') : null;
$sql.= !empty($emailID) ? " AND lower(P.EMAIL_ADDRESS) = '" . db2_escape_string(strtolower($emailID)) . "'  " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . db2_escape_string(strtolower($notesId)) . "'  " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . db2_escape_string(strtolower($cnum)) . "'  " : null;
$sql.= " ORDER BY P.NOTES_ID; ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $employees[] = array_map('trim',$row);
    }
} else {
    ob_clean();
    ob_start();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    exit();
}

$employees = count($employees)==1 ? $employees[0] : $employees;

ob_clean();
header('Content-Type: application/json');
echo json_encode($employees);

