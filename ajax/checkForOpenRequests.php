<?php

use itdq\AuditTable;
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestsTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$assetTitles = $assetRequestsTable->getOpenRequestsForCnum($_POST['cnum']);

$messages = ob_get_clean();
$sucess = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'cnum'=>$_POST['cnum'], 'assetTitles'=>$assetTitles);
AuditTable::audit("Concluded:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($response,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);
ob_clean();
echo json_encode($response);