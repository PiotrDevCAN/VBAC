<?php
use itdq\AuditTable;
use vbac\pesEventTable;
use vbac\allTables;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );
    $pesTracker->setPesProcessStatus($_POST['cnum'],$_POST['processStatus']);

    $comment = $pesTracker->savePesComment($_POST['cnum'],"Process Status set to " . $_POST['processStatus']);
    
    
    $messages  = ob_get_clean();
    $success   = empty($messages);
    
    $now = new DateTime();    
    $row = array('CNUM'=>$_POST['cnum'],'PROCESSING_STATUS'=>$_POST['processStatus'],'PROCESSING_STATUS_CHANGED'=>$now->format('Y-m-d H:i:s'));    
    
    ob_start();
    pesTrackerTable::formatProcessingStatusCell($row);
    $formattedStatusField = ob_get_clean();
    
} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;
    
}
$response = array('success'=>$success,'messages'=>$messages, 'formattedStatusField'=>$formattedStatusField, "comment"=>$comment);
echo json_encode($response);    