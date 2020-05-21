<?php


use itdq\AuditTable;
use vbac\personTable;
use vbac\allTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);

$rfStart = isset($_POST['rfStart']) ? $_POST['rfStart'] : null;
$rfEnd   = isset($_POST['rfEnd']) ? $_POST['rfEnd'] : null;

if(!empty($_POST['cnum'])){
    $personTable->updateRfFlag($_POST['cnum'], $_POST['rfFlag'],$rfStart,$rfEnd);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);