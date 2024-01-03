<?php
use vbac\personTable;
use vbac\allTables;
use vbac\personRecord;
use itdq\AuditTable;
use vbac\emails\pesRequestEmail;
use vbac\pesTrackerTable;

ob_start();

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {

    $cnum = $_POST['cnum'];
    $workerId = $_POST['workerid'];
    
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $pesTracker->createNewTrackerRecord($cnum, $workerId);

    $table = new personTable(allTables::$PERSON);
    $personData = $table->getWithPredicate(" CNUM='" . htmlspecialchars(trim($cnum)) . "' AND WORKER_ID='" . htmlspecialchars(trim($workerId)) . "' ");

    $person = new personRecord();
    $person->setFromArray($personData);

    $pesRequest = new pesRequestEmail();
    $pesRequest->sendPesRequest($person);

    $success = $table->setPesRequested($cnum, $workerId, $_SESSION['ssoEmail']);

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
ob_start();

$response = array('success'=>$success,'messages'=>$messages,'diags'=>$diags);
ob_clean();
echo json_encode($response);