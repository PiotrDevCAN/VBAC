<?php

use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);

$preboarderCnum = $_POST['preboarderCnum'];
$preboarderWorkerId = $_POST['preboarderWorkerId'];

$regularCnum = $_POST['cnum'];
$regularWorkerId = $_POST['workerid'];

$table = new personTable(allTables::$PERSON);
try {
    $table->linkPreBoarderToRegular($preboarderCnum, $preboarderWorkerId, $regularCnum, $regularWorkerId);
} catch (Exception $e) {
    echo $e->getMessage();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);