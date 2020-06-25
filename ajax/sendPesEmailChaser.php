<?php

use vbac\pesEmail;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\pesTrackerTable;
use vbac\personRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$cnum = $_POST['cnum'];
$emailAddress = personTable::getEmailFromCnum($cnum);

$pesEmailObj = new pesEmail();
$emailResponse = $pesEmailObj->sendPesEmailChaser($cnum, $emailAddress, $_POST['chaser'], $_POST['requestor']);

$emailStatus = $emailResponse['Status'];

$messages = ob_get_clean();
ob_start();
$success = strlen($messages)==0;
$response = array();
$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;
$response['pesStatus'] = personRecord::PES_STATUS_REQUESTED;

$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );

if($success){
    $dateObj = new DateTime();
    $dateLastChased = $dateObj->format('Y-m-d');
    $pesTracker->setPesDateLastChased($cnum, $dateLastChased);

    $messages = ob_get_clean();
    ob_start();
    $success = strlen($messages)==0;

    $response['success'] = $success;
    $response['messages'] = $messages;
    $response['lastChased'] = $dateObj->format('d M Y');;

    try {
        $pesTracker->savePesComment($cnum,"Automated PES Chaser Level " . $_POST['chaser'] . " sent to " . $_POST['emailaddress']);
        $pesTracker->savePesComment($cnum,"Automated PES Chaser Email Status :  " . $emailStatus);

        $comment = $pesTracker->getPesComment($cnum);
        $response['comment'] = $comment;

    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }

} else {
    try {
        $pesTracker->savePesComment($cnum,"Error trying to send automated PES Chaser Level " . $_POST['chaser'] . " to " .  $_POST['emailaddress']);
        $pesTracker->savePesComment($cnum,"Automated PES Email Status :  " . $emailStatus);
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
}

ob_clean();
echo json_encode($response);