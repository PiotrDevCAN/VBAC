<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$success = $assetRequestTable->deVarb($_REQUEST['varbref']);

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_REQUEST,true),'messages'=>$messages);

ob_clean();
echo json_encode($response);