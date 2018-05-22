<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\assetRequestRecord;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

$success = false;

foreach ($_POST['status'] as $reference => $statusIndicator){
    $status = trim($statusIndicator);
    echo "Ref:$reference Status:$status";
    $success = $assetRequestTable->setRequestsOrderItStatus($reference,$status);
    if(!$success){
        db2_rollback($_SESSION['conn']);
        break;
    }
}

if($success){
    db2_commit($_SESSION['conn']);
}

db2_autocommit($_SESSION['conn'],$autoCommit);

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_POST,true),'messages'=>$messages);

ob_clean();
echo json_encode($response);