<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

ob_start();
$assetRequestTable->mapVarbToOrderITForm();
$form = ob_get_clean();
ob_start();

$messages = ob_get_clean();
ob_start();
$response = array("form"=>$form,'messages'=>$messages);

ob_clean();
echo json_encode($response);