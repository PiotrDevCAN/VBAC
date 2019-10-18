<?php
use vbac\personRecord;
use vbac\allTables;
use vbac\personTable;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

AuditTable::audit("Completed Offboarding for " . $_POST['cnum']);

$table = new personTable(allTables::$PERSON);
$revalidationStatus = $table->getRevalidationFromCnum($_POST['cnum']);

if(!empty($_POST['cnum'])){
    $table->flagOffboarded($_POST['cnum'],$revalidationStatus);
} else {
    echo "No cnum provided";
}

$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'cnum'=>$_POST['CNUM'],'revalidationStatus'=>$revalidationStatus, 'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);