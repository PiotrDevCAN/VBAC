<?php

use vbac\pesEmail;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);
$pesEmailObj = new pesEmail();

$emailResponse = $pesEmailObj->sendPesEmail($_POST['firstname'],$_POST['lastname'],$_POST['emailaddress'], $_POST['country'], $_POST['openseat']);
$messages = ob_get_contents();
$success = strlen($messages)==0; 
$response = array();
$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;

if($success){
    $personTable = new personTable(allTables::$PERSON);
    $cnum = $personTable->getCnumFromEmail($_POST['emailaddress']);
    $personTable->setPesEvidence($cnum);
    
    $messages = ob_get_contents();
    $success = strlen($messages)==0;   
    
    $response['success'] = $success;
    $response['messages'] = $messages;
    
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );
    
    try {
        $pesTracker->savePesComment($_POST['psm_cnum'],"Automated PES Email requesting evidence sent to " . $_POST['emailaddress']);
        $pesTracker->savePesComment($_POST['psm_cnum'],"PES STATUS set to : " . $_POST['psm_status']); 
    } catch (Exception $e) {
        // Don't give up just because we didn't save the comment.
        echo $e->getMessage();
    }
    
}

ob_clean();
echo json_encode($response);