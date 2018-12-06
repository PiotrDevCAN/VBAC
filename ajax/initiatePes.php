<?php
use vbac\personTable;
use vbac\allTables;
use vbac\personRecord;
use itdq\AuditTable;
use vbac\pesTrackerTable;

ob_start();

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    $table = new personTable(allTables::$PERSON);
    $personData = $table->getWithPredicate(" CNUM='" . db2_escape_string(trim($_POST['cnum'])) . "' ");

    $person = new personRecord();
    $person->setFromArray($personData);
    $person->sendPesRequest();
    $success = $person->setPesRequested();
    
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $pesTracker->createNewTrackerRecord($_POST['cnum']);
    
    echo $success ? "PES Check initiated" : "Problem Initiating PES check";
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
ob_start();
echo "<pre>" . print_r($personData,true) . "</pre><br/>";
$diags = ob_get_clean();



$response = array('success'=>$success,'messages'=>$messages,'diags'=>$diags);
ob_clean();
echo json_encode($response);