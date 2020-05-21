<?php
use itdq\AuditTable;
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\assetRequestRecord;
use itdq\Loader;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);
$loader = new Loader();

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$reference      = trim($_POST['reference']);
$justification  = trim($_POST['justification']);
$status         = trim($_POST['status']);

$saved = $assetRequestTable->saveJustification($reference, $justification);

if($saved && $status==assetRequestRecord::STATUS_REJECTED){
    $assetRequestTable->setStatus($_POST['reference'], assetRequestRecord::STATUS_CREATED,'Justification Amended');
    $approverDetails = $loader->loadIndexed('APPROVER_EMAIL','REQUEST_REFERENCE',allTables::$ASSET_REQUESTS," REQUEST_REFERENCE='" . $reference . "' " );
    $approvingMgr = array($approverDetails[$reference]);
    $assetRequestTable->notifyApprovingMgr($approvingMgr);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages) && $saved;
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);