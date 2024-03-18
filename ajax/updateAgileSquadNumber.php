<?php
use itdq\AuditTable;
use vbac\allTables;
use vbac\personSquadRecord;
use vbac\personSquadTable;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personSquadTable = new personSquadTable(allTables::$EMPLOYEE_AGILE_MAPPING);
$personPrimaryData = $personSquadTable->getWithPredicate(" CNUM='" . trim($_POST['agileCnum']) . "' AND WORKER_ID='" . trim($_POST['agileWorkerId']) . "' AND TYPE='" . personSquadRecord::PRIMARY . "' ");

if (!empty($personPrimaryData)) {
    // record exists
    $personPrimaryData['SQUAD_NUMBER'] = $_POST['agileSquad'];
} else {
    // new record
    $personPrimaryData = array(
        'CNUM' => $_POST['agileCnum'],
        'WORKER_ID' => $_POST['agileWorkerId'],
        'SQUAD_NUMBER' => $_POST['agileSquad']
    );
}

$personSquadRecord = new personSquadRecord();
$personSquadRecord->setFromArray($personPrimaryData);

$personTable = new personTable(allTables::$PERSON);

$saveRecordResult = $personSquadTable->saveRecord($personSquadRecord);
$updateResult = $personTable->updateAgileSquadNumber($_POST['agileCnum'], $_POST['agileWorkerId'], $_POST['agileSquad'], $_POST['version']);

if(is_null($saveRecordResult)){
    echo json_encode(sqlsrv_errors());
}
if(!$updateResult){
    echo json_encode(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();

$success = empty($messages) && $updateResult && !is_null($saveRecordResult);

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);