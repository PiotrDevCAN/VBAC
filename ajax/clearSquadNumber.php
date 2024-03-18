<?php
use itdq\AuditTable;
use vbac\allTables;
use vbac\personSquadRecord;
use vbac\personTable;
use vbac\personSquadTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personSquadTable = new personSquadTable(allTables::$EMPLOYEE_AGILE_MAPPING);
$personPrimaryData = $personSquadTable->getWithPredicate(" CNUM='" . trim($_POST['cnum']) . "' AND WORKER_ID='" . trim($_POST['workerid']) . "' AND TYPE='" . personSquadRecord::PRIMARY . "' ");

$personSquadRecord = new personSquadRecord();
$personSquadRecord->setFromArray($personPrimaryData);

$personTable = new personTable(allTables::$PERSON);

if(!empty($_POST['cnum']) && !empty($_POST['workerid'])){
    $personSquadTable->deleteRecord($personSquadRecord);
    $personTable->clearSquadNumber($_POST['cnum'], $_POST['workerid'], $_POST['version']);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);