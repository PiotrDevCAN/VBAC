<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;
use vbac\personRecord;

ob_start();

if(!empty($_REQUEST['token'])){
    if($_REQUEST['token']!= $token){
        // echo "Incorrect token provided";
        return;
    }
} else {
    // echo "No token provided";
    return;
}

$predicate =  " AND ( " . personTable::inactivePersonPredicate() . ") ";

if(isset($_REQUEST['activeoffboarding'])){
    switch (trim($_REQUEST['activeoffboarding'])) {
        case 'Exc':
            $predicate = " AND REVALIDATION_STATUS like 'offboarding%' ";
            break;
        case 'Rev':
            $predicate = " AND ( ( ( " . personTable::inactivePersonPredicate() . ")  OR REVALIDATION_STATUS like 'offboarding%' ) ";
            $predicate.= "      OR  ( PES_STATUS = '" . personRecord::PES_STATUS_REVOKED .  "' AND  REVALIDATION_STATUS not like 'offboarded%' ) )  ";
            break;            
        default:
            $predicate = " AND ( ( " . personTable::inactivePersonPredicate() . ")  OR REVALIDATION_STATUS like 'offboarding%' ) ";
        break;
    }
}

$sql = " SELECT P.NOTES_ID, P.EMAIL_ADDRESS, P.PES_STATUS ";
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON . " AS P ";
$sql.= " WHERE 1=1 ";
$sql.= " AND trim(NOTES_ID) != ''  ";
$sql.=  $predicate;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($GLOBALS['conn'], $sql);
$employees = array();
$employeesArray = array();

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $rowTrimmed = array_map('trim',$row);
        $employeesArray[] = $rowTrimmed;
    }
    $employees = count($employeesArray)==1 ? $employeesArray[0] : $employeesArray;
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