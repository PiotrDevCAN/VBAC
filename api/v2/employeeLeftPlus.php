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
    $additionalSelect .= ", " . htmlspecialchars($field);
}


$sql = " SELECT P.NOTES_ID " . $additionalSelect;
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND " . personTable::inactivePersonPredicate();
$sql.= !empty($emailID) ? " AND (lower(P.EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "' OR lower(P.KYN_EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "') " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . htmlspecialchars(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . htmlspecialchars(strtolower($cnum)) . "'; " : null;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    ob_start();
    $response = array('success'=>false,'error'=>$errorMessage);
    echo json_encode($response);
    exit();
}

while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
    $rowTrimmed = array_map('trim', $row);
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
