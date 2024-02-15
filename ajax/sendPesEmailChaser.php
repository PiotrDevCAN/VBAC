<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\emails\pesChaserEmail;
use vbac\pesTrackerTable;
use vbac\personRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$cnum = $_POST['cnum'];
$workerid = $_POST['workerid'];
$emailAddress = $_POST['emailaddress'];
$chaser = $_POST['chaser'];
$flm = $_POST['flm'];

$person = new personRecord();
$person->setFromArray(
    array(
        'CNUM'=>$cnum,
        'WORKER_ID'=>$workerId,
        'EMAIL_ADDRESS'=>$emailAddress
    )
);
$pesEmailObj = new pesChaserEmail();
$emailResponse = $pesEmailObj->send($person, $chaser, $flm);

$emailStatus = $emailResponse['Status'];

$messages = ob_get_clean();
ob_start();
$success = strlen($messages)==0;
$response = array();
$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;
$response['pesStatus'] = personRecord::PES_STATUS_REQUESTED;

$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);

if($success){
    $dateObj = new DateTime();
    $dateLastChased = $dateObj->format('Y-m-d');
    $pesTracker->setPesDateLastChased($cnum, $workerId, $dateLastChased);

    $messages = ob_get_clean();
    ob_start();
    $success = strlen($messages)==0;

    $response['success'] = $success;
    $response['messages'] = $messages;
    $response['lastChased'] = $dateObj->format('d M Y');;

    try {
        $pesTracker->savePesComment($cnum, $workerId, "Automated PES Chaser Level " . $chaser . " sent to " . $emailAddress);
        $pesTracker->savePesComment($cnum, $workerId, "Automated PES Chaser Email Status :  " . $emailStatus);

        $comment = $pesTracker->getPesComment($cnum, $workerId);
        $response['comment'] = $comment;

    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }

} else {
    try {
        $pesTracker->savePesComment($cnum, $workerId, "Error trying to send automated PES Chaser Level " . $chaser . " to " .  $emailAddress);
        $pesTracker->savePesComment($cnum, $workerId, "Automated PES Email Status :  " . $emailStatus);
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
}

ob_clean();
echo json_encode($response);