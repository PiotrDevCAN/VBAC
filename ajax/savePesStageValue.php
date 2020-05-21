<?php
use itdq\AuditTable;
use vbac\pesEventTable;
use vbac\allTables;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {

    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );
    $pesTracker->setPesStageValue($_POST['cnum'],$_POST['stage'], $_POST['stageValue']);
    $comment = $pesTracker->savePesComment($_POST['cnum'],"Stage " . $_POST['stage'] . " Set to " . $_POST['stageValue']);

    $messages  = ob_get_clean();
    ob_start();
    $success   = empty($messages);

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response = array('success'=>$success,'messages'=>$messages, 'comment'=>$comment);
echo json_encode($response);