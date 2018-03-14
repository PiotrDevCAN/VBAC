<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$success = $assetRequestTable->saveVarbToOrderItMapping($_POST['ORDERIT_NUMBER'], $_POST['unmappedVarb'], $_POST['request']);

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_POST['request'],true),'messages'=>$messages);

ob_clean();
echo json_encode($response);