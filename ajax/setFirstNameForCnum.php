<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);


if(!empty($_POST['cnum'])){
    $personTable->setFirstName($_POST['cnum'],$_POST['firstname']);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);