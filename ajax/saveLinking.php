<?php

use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);

$personCnum = $_POST['person_preboarded'];
$regularId = $_POST['ibmer_preboarded'];

$table = new personTable(allTables::$PERSON);
try {
    // regular ID
    $table->linkPreBoarderToRegular($personCnum, $regularId);
} catch (Exception $e) {
    echo $e->getMessage();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);