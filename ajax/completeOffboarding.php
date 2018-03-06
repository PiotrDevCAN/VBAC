<?php
use vbac\personRecord;
use vbac\allTables;
use vbac\personTable;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

AuditTable::audit("Completed Offboarding for " . $_POST['cnum']);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

if(!empty($_POST['cnum'])){
    $table->flagOffboarded($_POST['cnum']);
} else {
    echo "No cnum provided";
}



$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,'cnum'=>$_POST['CNUM'], 'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);