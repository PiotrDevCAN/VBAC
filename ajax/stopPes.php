<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\emails\pesTeamOfOffStopRequestEmail;
use vbac\personRecord;
use vbac\pesStatusChangeNotification;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$comment = null;

try {

    $cnum = $_POST['psm_cnum'];
    $workerId = $_POST['psm_worker_id'];
    $requestor = $_POST['psm_pesrequestor'];

    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $pesTracker->savePesComment($cnum, $workerId, "PES Stop Requested");
    
    $basicPersonDetails = array(
        'CNUM'=>$cnum,
        'WORKER_ID'=>$workerId
    );
    $person = new personRecord();
    $person->setFromArray(
        $basicPersonDetails
    );
    $email = new pesTeamOfOffStopRequestEmail();
    $email->send($person, $requestor) ;
    
    $comment = $pesTracker->getPesComment($cnum, $workerId);

    $success = true;
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
    $notificationStatus = pesStatusChangeNotification::EMAIL_NOT_APPLICABLE_ERROR;
}

$messages = ob_get_clean();
ob_start();
$success = $success && empty($messages);
$response = array(
    'success' => $success,
    'messages' => $messages,
    'comment' => $comment
);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));