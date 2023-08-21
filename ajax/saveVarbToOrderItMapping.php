<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;
use vbac\assetRequestRecord;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

$personTable = new personTable(allTables::$PERSON);
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);


foreach ($_POST['orderit'] as $requestReference => $orderIt){
    if(!empty(trim($orderIt))){
        echo "LBG : $orderIt Ref: $requestReference ";
        $assetRequestTable->saveRefToOrderItMapping($orderIt, $requestReference);
    }
}

// $success = $assetRequestTable->saveVarbToOrderItMapping($_POST['ORDERIT_NUMBER'], $_POST['unmappedVarb'], $_POST['request']);

if(!empty($_POST['comment'])){
    foreach ($_POST['comment'] as $requestReference => $comment){
        $assetRequestTable->updateCommentForOrderItStatus($requestReference, $comment);
    }
}





// if(!empty($_POST['primaryUid'])){
//     foreach ($_POST['primaryUid'] as $reference => $primaryUid){
//         $secondaryUid = !empty($_POST['secondaryUid'][$reference]) ? $_POST['secondaryUid'][$reference] : '';

//         if(!empty($primaryUid)){
//             $assetRequestTable->updateUids($reference, trim($primaryUid), trim($secondaryUid));
//             $assetRequestTable->setToProvisionedStatus($reference);
//             $requestDetails = $assetRequestTable->getCnumAndAssetForReference($reference);
//             if($requestDetails){
//                 $personTable->assetUpdate($requestDetails['cnum'], $requestDetails['assetTitle'], $primaryUid);
//             }
//             $assetRequestTable->setRequestsOrderItStatus($reference, assetRequestRecord::STATUS_ORDERIT_APPROVED);
//         }
//     }
// }


sqlsrv_commit($GLOBALS['conn']);
db2_autocommit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();

$response = array('result'=>$success,'post'=>print_r($_POST,true),'messages'=>$messages);
ob_clean();
echo json_encode($response);