<?php

use itdq\AuditTable;
use vbac\pesEmail;
use vbac\allTables;
use vbac\pesTrackerTable;
use vbac\personRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$emailAddress = $_POST['emailaddress'];
$country = $_POST['country'];
$openSeat = $_POST['openseat'];
$cnum = $_POST['cnum'];
$workerid = $_POST['workerid'];
$recheck = $_POST['recheck'];

$pesEmailObj = new pesEmail();
$emailResponse = $pesEmailObj->sendPesEmail($firstName, $lastName, $emailAddress, $country, $openSeat, $cnum, $recheck);

$emailStatus = $emailResponse['Status'];
$emailStatusMessage = $emailResponse['sendResponse']['response'];

$messages = ob_get_clean();
ob_start();

$isrecheck  = ($recheck=='yes');
$recheckWording  = $isrecheck ? "(recheck)" : null;
$success = strlen($messages)==0;
$response = array();
$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;
$response['pesStatus'] = $isrecheck ?  personRecord::PES_STATUS_RECHECK_PROGRESSING : personRecord::PES_STATUS_REQUESTED;

$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);

if($success){
    $isrecheck ? $personTable->setPesStatus($cnum, $workerid, personRecord::PES_STATUS_RECHECK_PROGRESSING)  : $personTable->setPesEvidence($cnum, $workerid);

    $messages = ob_get_clean();
    ob_start();
    $success = strlen($messages)==0;

    $response['success'] = $success;
    $response['messages'] = $messages;
    $response['recheck'] = $recheck;

    try {
        $pesTracker->savePesComment($cnum, $workerid, "Automated PES Email $recheckWording requesting evidence sent to " . $emailAddress);
        $pesTracker->savePesComment($cnum, $workerid, "Automated PES Email $recheckWording Status :  " . $emailStatus);
        $pesTracker->savePesComment($cnum, $workerid, "Automated PES Email $recheckWording Status Message :  " . $emailStatusMessage);

        $comment = $pesTracker->getPesComment($cnum, $workerid);
        $response['comment'] = $comment;

    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }

} else {
    try {
        $pesTracker->savePesComment($cnum, $workerid, "Error trying to send automated PES Email $recheckWording " . $emailAddress);
        $pesTracker->savePesComment($cnum, $workerid, "Automated PES Email $recheckWording Status :  " . $emailStatus);
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
}

ob_clean();
echo json_encode($response);