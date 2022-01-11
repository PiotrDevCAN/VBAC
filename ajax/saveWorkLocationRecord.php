<?php
use vbac\allTables;
use itdq\AuditTable;
use itdq\FormClass;
use vbac\workLocationRecord;
use vbac\workLocationTable;

ob_start();

$country = !empty($_POST['COUNTRY']) ? trim($_POST['COUNTRY']) : null;
$city = !empty($_POST['CITY']) ? trim($_POST['CITY']) : null;
$address = !empty($_POST['ADDRESS']) ? trim($_POST['ADDRESS']) : null;
$onShore = !empty($_POST['ONSHORE']) ? trim($_POST['ONSHORE']) : null;
$cbcInPlace = !empty($_POST['CBC_IN_PLACE']) ? trim($_POST['CBC_IN_PLACE']) : null;
$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$id = !empty($_POST['ID']) ? trim($_POST['ID']) : null;

if ($country == null || $city == null || $address == null || $onShore == null || $cbcInPlace == null || $mode == '') {
    $invalidOtherParameters = true;
} else {
    $invalidOtherParameters = false;
}

if (empty($id) && $mode == FormClass::$modeEDIT) {
    $invalidRecordId = true;
} else {
    $invalidRecordId = false;
}

// default validation values
$saveResult = false;
$success = false;
$messages = '';
$additionalFields = '';

switch (true) {
    case $invalidOtherParameters:
        // required parameters protection
        $messages = 'Significant parameters from form are missing.';
        break;
    case $invalidRecordId:
        // missing ID of record
        $messages = 'Missing ID of the edited record.';
        break;
    default:
        
        $workLocationRecord = new workLocationRecord();
        $workLocationRecord->setFromArray($_POST);
        
        switch($_POST['ONSHORE']) {
            case 'Yes':
                $onShore = '1';
                break;
            default:
                $onShore = '0';
                break;
        }
        
        switch($_POST['CBC_IN_PLACE']) {
            case 'Yes':
                $CBCInPlace = $_POST['CBC_IN_PLACE'];
                break;
            default:
                $CBCInPlace = '';
                break;
        }
        
        $additionalFields = array();
        $additionalFields['ONSHORE'] = $onShore;
        $additionalFields['CBC_IN_PLACE'] = $CBCInPlace;
        if (!empty($additionalFields)) {
            $workLocationRecord->setFromArray($additionalFields);
        }
        
        $table = allTables::$STATIC_LOCATIONS;
        
        $workLocationTable = new workLocationTable($table);
        
        $saveRecordResult = $workLocationTable->saveRecord($workLocationRecord);
        if(($saveRecordResult && $_POST['mode']==FormClass::$modeDEFINE) || (!$saveRecordResult && $_POST['mode']==FormClass::$modeEDIT)){
            $success = true;
        } else {
            $success = false;
        }
        
        $messages = ob_get_clean();

        break;
}

ob_start();

$response = array(
    'result'=>$saveResult,
    'success'=>$success,
    'messages'=>$messages,
    'parms'=>print_r($_POST,true),
    'parms_2'=>$additionalFields
);
ob_clean();
echo json_encode($response);
