<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;
use vbac\personRecord;

ob_start();


if($_REQUEST['token']!= $token){
    return;
}

$predicate =  " AND ( " . personTable::activePersonPredicate() . ") ";

if(isset($_REQUEST['activeoffboarding'])){
    switch (trim($_REQUEST['activeoffboarding'])) {
        case 'Exc':
            $predicate = " AND REVALIDATION_STATUS like 'offboarding%' ";
            break;
        case 'Rev':
            $predicate = " AND ( ( ( " . personTable::activePersonPredicate() . ")  OR REVALIDATION_STATUS like 'offboarding%' ) ";
            $predicate.= "      OR  ( PES_STATUS = '" . personRecord::PES_STATUS_REVOKED .  "' AND  REVALIDATION_STATUS not like 'offboarded%' ) )  ";
            break;            
        default:
            $predicate = " AND ( ( " . personTable::activePersonPredicate() . ")  OR REVALIDATION_STATUS like 'offboarding%' ) ";
        break;
    }
}

$sql = " SELECT P.NOTES_ID ";
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 ";
$sql.= " AND trim(NOTES_ID) != ''  ";
$sql.=  $predicate;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($GLOBALS['conn'], $sql);
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