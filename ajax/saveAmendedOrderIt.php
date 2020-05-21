<?php
use itdq\AuditTable;
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);
$loader = new Loader();

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$reference      = trim($_POST['reference']);
$newOrderit     = trim($_POST['newOit']);

$saved = $assetRequestTable->saveAmendedOit($reference, $newOrderit);

$messages = ob_get_clean();
ob_start();
$success = empty($messages) && $saved;
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);