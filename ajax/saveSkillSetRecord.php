<?php
use vbac\allTables;
use itdq\AuditTable;
use itdq\FormClass;
use vbac\skillSetRecord;
use vbac\skillSetTable;

ob_start();

$skillSet = !empty($_POST['SKILLSET']) ? trim($_POST['SKILLSET']) : null;
$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$id = !empty($_POST['SKILLSET_ID']) ? trim($_POST['SKILLSET_ID']) : null;

if ($skillSet == null || $mode == '') {
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
        
        $skillSetRecord = new skillSetRecord();
        $skillSetRecord->setFromArray($_POST);
        
        $table = allTables::$STATIC_SKILLSETS;
        
        $skillSetTable = new skillSetTable($table);
        
        $saveRecordResult = $skillSetTable->saveRecord($skillSetRecord);
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
