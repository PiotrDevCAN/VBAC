<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$success = $assetRequestTable->deVarb($_REQUEST['varbref']);

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_REQUEST,true),'messages'=>$messages);

ob_clean();
echo json_encode($response);