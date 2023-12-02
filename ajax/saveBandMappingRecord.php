<?php
use vbac\allTables;
use itdq\AuditTable;
use itdq\FormClass;
use vbac\bandMappingRecord;
use vbac\bandMappingTable;

ob_start();

$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$id = !empty($_POST['BUSINESS_TITLE']) ? trim($_POST['BUSINESS_TITLE']) : null;
$band = !empty($_POST['BAND']) ? trim($_POST['BAND']) : null;

if ($band == null || $mode == '') {
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
        
        $bandMappingRecord = new bandMappingRecord();
        $bandMappingRecord->setFromArray($_POST);
        
        $table = allTables::$BUSINESS_TITLE_MAPPING;
        
        $bandMappingTable = new bandMappingTable($table);
        
        $saveRecordResult = $bandMappingTable->saveRecord($bandMappingRecord);
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
