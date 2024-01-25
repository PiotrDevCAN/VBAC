<?php
use itdq\AuditTable;
use itdq\PhpMemoryTrace;
use vbac\allTables;
use vbac\personTable;
use vbac\personRecord;
use vbac\emails\pesRequestEmail;
use vbac\pesTrackerTable;

ob_start();

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personData = array();

PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);

try {

    // Takes raw data from the request
    $json = file_get_contents('php://input');

    // Converts it into a PHP object
    $data = json_decode($json, true);

    $cnum = isset($data['cnum']) ? $data['cnum'] : null;
    $workerId = isset($data['workerid']) ? $data['workerid'] : null;

    if (!$cnum) {
        throw new \Exception('No CNUM provided in ' . __METHOD__);
    }
    if (!$workerId) {
        throw new \Exception('No WORKER ID provided in ' . __METHOD__);
    }

    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $return = $pesTracker->createNewTrackerRecord($cnum, $workerId);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);

    $table = new personTable(allTables::$PERSON);
    $personData = $table->getWithPredicate(" CNUM='" . trim($cnum) . "' AND WORKER_ID='" . trim($workerId) . "' ");
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);
    
    $person = new personRecord();
    $person->setFromArray($personData);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);

    $pesRequest = new pesRequestEmail();
    $pesRequest->sendPesRequest($person);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);

    $success = $table->setPesRequested($cnum, $workerId, $_SESSION['ssoEmail']);

    echo $success ? "PES Check initiated" : "Problem Initiating PES check";
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true);
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