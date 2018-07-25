<?php


use itdq\AuditTable;
use vbac\personTable;
use vbac\allTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);

if(!empty($_POST['cnum'])){
    $personTable->updateRfFlag($_POST['cnum'], $_POST['rfFlag']);
}

$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);