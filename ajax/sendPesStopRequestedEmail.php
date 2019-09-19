<?php

use vbac\pesEmail;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\pesTrackerTable;
use vbac\personRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_AUDIT);

$cnum = $_POST['psm_cnum'];
$requestor = $_POST['psm_pesrequestor'];
$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
$response = array();
$response['success']=false;

try {
    pesEmail::notifyPesTeamOfOffStopRequest($cnum, $requestor) ;
     $pesTracker->savePesComment($cnum,"PES Stop Requested");

     $comment = $pesTracker->getPesComment($cnum);
     $response['comment'] = $comment;

     $messages = ob_get_contents();
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