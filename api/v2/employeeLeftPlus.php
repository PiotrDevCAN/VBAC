<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if(!empty($_REQUEST['token'])){
    if($_REQUEST['token']!= $token){
        // echo "Incorrect token provided";
        return;
    }
} else {
    // echo "No token provided";
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;

$additionalFields = !empty($_REQUEST['plus']) ? explode(",", $_REQUEST['plus']) : array('EMAIL_ADDRESS');
$additionalSelect = null;
$employeesArray = array();

foreach ($additionalFields as $field) {
    $additionalSelect .= ", " . db2_escape_string($field);
}


$sql = " SELECT P.NOTES_ID " . $additionalSelect;
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND " . personTable::inactivePersonPredicate();
$sql.= isset($_GET['emailid']) ? " AND lower(P.EMAIL_ADDRESS) = '" . db2_escape_string(strtolower($emailID)) . "' " : null;
$sql.= isset($_GET['notesid']) ? " AND lower(P.NOTES_ID) = '" . db2_escape_string(strtolower($notesId)) . "' " : null;
$sql.= isset($_GET['cnum']) ? " AND lower(P.CNUM) = '" . db2_escape_string(strtolower($cnum)) . "' " : null;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    ob_start();
    $response = array('success'=>false,'error'=>$errorMessage);
    echo json_encode($response);
    exit();
}

while(($row = db2_fetch_assoc($rs))==true){
    $rowTrimmed = array_map('trim',$row);
    $employeesArray[] = $rowTrimmed;
}
$employees = count($employeesArray)==1 ? $employeesArray[0] : $employeesArray;
$errorMessage = ob_get_clean();
ob_start();
$success = empty($errorMessage);
$response = array('success'=>$success
    ,'employees'=>$employees, 'employeesArray'=>print_r($employeesArray,true)
    ,'error'=>$errorMessage
    ,'rs'=>$rs,'sql'=>$sql,'notesid'=>$notesId,'plus'=>$additionalFields,'addSel'=>$additionalSelect
    ,'GET'=>print_r($_GET,true), 'REQUEST'=>print_r($_REQUEST,true));

if(!isset($_GET['diags'])){
    $response = $employees;
}
$responseJson = json_encode($response);
if($responseJson){
    echo $responseJson;
} else {
    var_dump($response);
}
