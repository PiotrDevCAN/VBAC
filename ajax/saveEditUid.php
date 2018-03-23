<?php

use itdq\AuditTable;
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$assetRequestTable->updateUids($_POST['reference'], trim($_POST['primaryUid']), trim($_POST['secondaryUid']));

$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);