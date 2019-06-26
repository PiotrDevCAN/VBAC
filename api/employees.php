<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

ob_start();


if($_REQUEST['token']!= $token){
    return;
}

$sql = " SELECT P.NOTES_ID ";
$sql.= " FROM " . $_SERVER['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND ( " . personTable::activePersonPredicate() . ") ";
$sql.= isset($_REQUEST['activeoffboarding']) ? " OR ( PES_STATUS like 'Cleared%' AND REVALIDATION_STATUS like 'offboarding%' ) "  : null;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($_SESSION['conn'], $sql);
$employees = array();

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $employees[] = trim($row['NOTES_ID']);
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    exit();
}

ob_clean();
$json = json_encode($employees);
echo $json;