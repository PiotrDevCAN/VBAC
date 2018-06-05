<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$table = !empty($_REQUEST['orderit']) ||  !empty($_REQUEST['varb']) ||   !empty($_REQUEST['ref'])?  $assetRequestTable->getAssetRequestsForOrderIt($_REQUEST['orderit'],$_REQUEST['varb'],$_REQUEST['ref']) : array('REQUEST_REFERENCE'=>null,'EMAIL'=>null,'ASSET'=>null,'STATUS'=>null);
$messages = ob_get_clean();
$response = array("data"=>$table,'messages'=>$messages,'orderit'=>$_REQUEST['orderit'],'request'=>print_r($_REQUEST,true));

ob_clean();
echo json_encode($response);