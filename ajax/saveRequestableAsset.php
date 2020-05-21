<?php
use vbac\requestableAssetListRecord;
use vbac\requestableAssetListTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();
$requesableAssetListTable = new requestableAssetListTable(allTables::$REQUESTABLE_ASSET_LIST);
$requesableAssetRecord = new requestableAssetListRecord();

$_POST['PROMPT'] = !empty($_POST['PROMPT']) ? urlencode($_POST['PROMPT']) : null;

$requesableAssetRecord->setFromArray($_POST);

AuditTable::audit("Saved RequestableAssetListRecord:<pre>". print_r($requesableAssetRecord,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);


$saveRecordResult = $requesableAssetListTable->saveRecord($requesableAssetRecord);
if(($saveRecordResult && $_POST['mode']=='Save') || (!$saveRecordResult && $_POST['mode']=='Update')){
    $success = true;
} else {
    $success = false;
}

$messages = ob_get_clean();
ob_start();
$response = array('success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);