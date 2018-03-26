<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;

ob_start();

$personTable = new personTable(allTables::$PERSON);
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$success = $assetRequestTable->saveVarbToOrderItMapping($_POST['ORDERIT_NUMBER'], $_POST['unmappedVarb'], $_POST['request']);


foreach ($_POST['primaryUid'] as $reference => $primaryUid){
    $secondaryUid = !empty($_POST['secondaryUid'][$reference]) ? $_POST['secondaryUid'][$reference] : '';  
    
    if(!empty($primaryUid)){
        $assetRequestTable->updateUids($reference, trim($primaryUid), trim($secondaryUid));
        $requestDetails = $assetRequestTable->getCnumAndAssetForReference($reference);
        if($requestDetails){
            $personTable->assetUpdate($requestDetails['cnum'], $requestDetails['assetTitle'], $primaryUid);
        }  
    }
}

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_POST['request'],true),'messages'=>$messages);

ob_clean();
echo json_encode($response);