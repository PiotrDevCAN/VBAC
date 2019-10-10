<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if($_REQUEST['token']!= $token){
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$onlyActive = !empty($_GET['onlyactive']) ? $_GET['onlyactive'] : true;

$additionalFields = !empty($_REQUEST['plus']) ? explode(",", $_REQUEST['plus']) : null;
$additionalSelect = null;
$employees = array();

foreach ($additionalFields as $field) {
    $additionalSelect .= ", " . db2_escape_string($field);
}


$sql = " SELECT P.NOTES_ID " . $additionalSelect;
$sql.= " FROM " . $_SERVER['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != '' ";
$sql.= $onlyActive ? " AND " . personTable::activePersonPredicate() : null;
$sql.= !empty($emailID) ? " AND lower(P.EMAIL_ADDRESS) = '" . db2_escape_string(strtolower($emailID)) . "'; " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . db2_escape_string(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . db2_escape_string(strtolower($cnum)) . "'; " : null;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($_SESSION['conn'], $sql);

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $employees[] = $row;
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
}

$employees = count($employees)==1 ? $employees[0] : $employees;

ob_clean();
echo json_encode($employees);

