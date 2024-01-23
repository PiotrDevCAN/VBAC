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

PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);

try {

    $cnum = isset($_POST['cnum']) ? $_POST['cnum'] : null;
    $workerId = isset($_POST['workerid']) ? $_POST['workerid'] : null;

    if (!$cnum) {
        throw new \Exception('No CNUM provided in ' . __METHOD__);
    }
    if (!$workerId) {
        throw new \Exception('No WORKER ID provided in ' . __METHOD__);
    }
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $return = $pesTracker->createNewTrackerRecord($cnum, $workerId);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);

    $table = new personTable(allTables::$PERSON);
    $personData = $table->getWithPredicate(" CNUM='" . trim($cnum) . "' AND WORKER_ID='" . trim($workerId) . "' ");
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);
    
    $person = new personRecord();
    $person->setFromArray($personData);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);

    $pesRequest = new pesRequestEmail();
    $pesRequest->sendPesRequest($person);
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);

    // $success = $table->setPesRequested($cnum, $workerId, $_SESSION['ssoEmail']);
    $success = false;

    // echo $success ? "PES Check initiated" : "Problem Initiating PES check";
    echo "Due to technical issues the functionality is under tests";
    PhpMemoryTrace::reportPeek(__FILE__,__LINE__,true,true);

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