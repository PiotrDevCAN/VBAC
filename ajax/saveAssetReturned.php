<?php
use itdq\AuditTable;
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\assetRequestRecord;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

if(!empty($_POST['primaryUid'])){
    $assetRequestTable->updateUids($_POST['reference'], trim($_POST['primaryUid']), trim($_POST['secondaryUid']));
}

$assetRequestTable->setStatus($_POST['reference'], assetRequestRecord::STATUS_RETURNED,'Asset Returned/Removed', trim($_POST['DATE_RETURNED']));


$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);