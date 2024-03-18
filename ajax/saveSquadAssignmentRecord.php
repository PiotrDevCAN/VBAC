<?php
use vbac\allTables;
use itdq\FormClass;
use vbac\personSquadRecord;
use vbac\personSquadTable;

ob_start();

$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';

$id = !empty($_POST['ID']) ? trim($_POST['ID']) : null;
$emailAddress = !empty($_POST['EMAIL_ADDRESS']) ? trim($_POST['EMAIL_ADDRESS']) : null;
$cnum = !empty($_POST['CNUM']) ? trim($_POST['CNUM']) : null;
$workerId = !empty($_POST['WORKER_ID']) ? trim($_POST['WORKER_ID']) : null;
$squadNumber = !empty($_POST['SQUAD_NUMBER']) ? trim($_POST['SQUAD_NUMBER']) : null;
$type = !empty($_POST['TYPE']) ? trim($_POST['TYPE']) : null;

$personSquadTable = new personSquadTable(allTables::$EMPLOYEE_AGILE_MAPPING);

$invalidOtherParameters = false;
$invalidRecordId = false;
$alreadyExists = false;
$primaryAlreadyExists = false;
$definePrimaryFirst = false;

if ($emailAddress == null || $cnum == null || $workerId == null || $squadNumber == null || $type == null || $mode == '') {
    $invalidOtherParameters = true;
} else {

    // check if record already exists
    $personSquadData = $personSquadTable->getWithPredicate(" SQUAD_NUMBER='" . trim($squadNumber) . "' AND CNUM='" . trim($cnum) . "' AND WORKER_ID='" . trim($workerId) . "' ");
    if (!empty($personSquadData)) {
        $alreadyExists = true;
    }

    if ($alreadyExists == false) {

        // check if Primary record already exists
        $personPrimaryData = $personSquadTable->getWithPredicate(" CNUM='" . trim($cnum) . "' AND WORKER_ID='" . trim($workerId) . "' AND TYPE='" . personSquadRecord::PRIMARY . "' ");

        // check if PRIMARY record already exists
        switch ($type) {
            case personSquadRecord::PRIMARY;
                if (!empty($personPrimaryData)) {
                    // we can have only one Primary record
                    $primaryAlreadyExists = true;
                }
                break;
            case personSquadRecord::SECONDARY;
                if (empty($personPrimaryData)) {
                    $definePrimaryFirst = true;
                }
                break;
            default:
                break;
        }
    }
}

if (empty($id) && $mode == FormClass::$modeEDIT) {
    $invalidRecordId = true;    
}

// default validation values
$saveResult = false;
$success = false;
$messages = '';

switch (true) {
    case $invalidOtherParameters:
        // required parameters protection
        $messages = 'Significant parameters from form are missing.';
        break;
    case $invalidRecordId:
        // missing ID of record
        $messages = 'Missing ID of the edited record.';
        break;
    case $alreadyExists:
        // record already exists
        $messages = 'Specified assignment to Squad already exists.';
        break;
    case $primaryAlreadyExists:
        // record already exists
        $messages = 'Primary assignment to Squad already exists.';
        break;
    case $definePrimaryFirst:
        // no primary record
        $messages = 'Primary Squad Assignment must be defined prior to defining the additional record.';
        break;
    default:
        
        $personSquadRecord = new personSquadRecord();
        $personSquadRecord->setFromArray($_POST);
        
        $saveRecordResult = $personSquadTable->saveRecord($personSquadRecord);
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
    'parms'=>print_r($_POST,true)
);
ob_clean();
echo json_encode($response);
