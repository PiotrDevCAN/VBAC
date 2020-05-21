<?php
use itdq\AuditTable;
use vbac\personTable;
use vbac\allTables;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$personTable->transferIndividual($_POST['transferCnum'], $_POST['transferToCnum']);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('result'=>$success,'post'=>print_r($_REQUEST,true),'messages'=>$messages);
ob_clean();
echo json_encode($response);