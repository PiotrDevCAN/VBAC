<?php

use vbac\pesEmail;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\pesTrackerTable;
use vbac\personRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$pesEmailObj = new pesEmail();

$emailResponse = $pesEmailObj->sendPesEmail($_POST['firstname'],$_POST['lastname'],$_POST['emailaddress'], $_POST['country'], $_POST['openseat']);

$emailStatus = $emailResponse['Status']->status;

$messages = ob_get_contents();
$success = strlen($messages)==0; 
$response = array();
$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;
$response['pesStatus'] = personRecord::PES_STATUS_REQUESTED;

$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );

if($success){
    $personTable = new personTable(allTables::$PERSON);
    $cnum = $personTable->getCnumFromEmail($_POST['emailaddress']);
    $personTable->setPesEvidence($cnum);
    
    $messages = ob_get_contents();
    $success = strlen($messages)==0;   
    
    $response['success'] = $success;
    $response['messages'] = $messages;
    
    try {
        $pesTracker->savePesComment($cnum,"Automated PES Email requesting evidence sent to " . $_POST['emailaddress']);
        $pesTracker->savePesComment($cnum,"Automated PES Email Status :  " . $emailStatus);
        
        $comment = $pesTracker->getPesComment($cnum);        
        $response['comment'] = $comment;       
        
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
    
} else {
    try {
        $pesTracker->savePesComment($cnum,"Error trying to send automated PES Email " . $_POST['emailaddress']);
        $pesTracker->savePesComment($cnum,"Automated PES Email Status :  " . $emailStatus);
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
}

ob_clean();
echo json_encode($response);