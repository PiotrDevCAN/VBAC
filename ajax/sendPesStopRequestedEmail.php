<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\pesEmail;
use vbac\pesTrackerTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$cnum = $_POST['psm_cnum'];
$workerId = $_POST['psm_worker_id'];
$requestor = $_POST['psm_pesrequestor'];
$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
$response = array();
$response['success']=false;

try {
    pesEmail::notifyPesTeamOfOffStopRequest($cnum, $workerId, $requestor) ;
    $pesTracker->savePesComment($cnum, $workerId, "PES Stop Requested");

    $comment = $pesTracker->getPesComment($cnum, $workerId);
    $response['comment'] = $comment;

    $messages = ob_get_clean();
    ob_start();
    $success = strlen($messages)==0;

    $response['success'] = $success;
    $response['messages'] = $messages;

} catch (Exception $e) {
    // Don't give up just because we didn't save the comment.
    ob_start();
    echo $e->getMessage();
    $messages = ob_get_flush();
    $success = strlen($messages)==0;
    $response['success'] = $success;
    $response['messages'] = $messages;
}

ob_clean();
echo json_encode($response);