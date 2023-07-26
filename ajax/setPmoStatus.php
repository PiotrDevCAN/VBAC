<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

if(!empty($_POST['cnum'])){
    $personTable = new personTable(allTables::$PERSON);
    $personTable->setPmoStatus($_POST['cnum'],$_POST['setpmostatusto']);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);