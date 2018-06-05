<?php
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$table = !empty($_REQUEST['varb']) ||  !empty($_REQUEST['ref']) ?  $assetRequestTable->getAssetRequestsForVarb($_REQUEST['varb'],$_REQUEST['ref']) : array('INCLUDED'=>null,'REQUEST_REFERENCE'=>null,'PERSON'=>null,'ASSET'=>null);
$messages = ob_get_clean();
$response = array("data"=>$table,'messages'=>$messages,'varb'=>$_REQUEST['varb'],'request'=>print_r($_REQUEST,true));

ob_clean();
echo json_encode($response);