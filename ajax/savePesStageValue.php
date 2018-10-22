<?php
use itdq\AuditTable;
use vbac\pesEventTable;
use vbac\allTables;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    
    $pesTracker = new pesTrackerTable(allTables::$PES_EVENTS);
    $pesTracker->setPesStageValue($_GET['cnum'],$_GET['stage'], $_GET['stageValue']);
    
    $messages  = ob_get_clean();
    $success   = empty($messages);
 
    return;    
} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;
    
}
$response = array('success'=>$success,'messages'=>$messages);
echo json_encode($response);     