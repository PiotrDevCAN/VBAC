<?php
use vbac\assetRequestsTable;
use itdq\AuditTable;
use vbac\allTables;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$updateResult = $personTable->updateAgileSquadNumber($_POST['agileCnum'],$_POST['agileSquad'], $_POST['version']);

if(!$updateResult){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_clean();
ob_start();

$success = empty($messages) && $updateResult;

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);