<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$status = personRecord::PES_STATUS_PES_PROGRESSING;

if(isset($_REQUEST['cnum']) && !empty($_POST['cnum']) && isset($_REQUEST['workerid']) && !empty($_POST['workerid'])) {
    if(is_array($_POST['cnum']) && is_array($_POST['workerid'])) {
        $cnums = $_POST['cnum'];
        $workerIds = $_POST['workerid'];
        foreach($cnums as $key => $cnum) {
            $workerId = $workerIds[$key];
            $personTable->setPesStatus($cnum, $workerId, $status);
        }
    } else {
        $personTable->setPesStatus($_POST['cnum'], $_POST['workerid'], $status);
    }
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);